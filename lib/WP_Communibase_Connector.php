<?php

use Communibase\Connector;

/**
 * TODO inject caching strategy/strategies .. current is Transient_API (i.e. db)
 *
 * A Wordpress Tool for accessing the Communibase API. This is a viable alternative to using the Connector directly.
 *
 * This Tool instantiates and delegates to a <code>Communibase\Connector</code> instance.
 * The following Wordpress options can be used to instantiate the Connector:
 * <code>communibase.api_key</code> (required)
 * <code>communibase.api_url</code> (optional)
 * <code>communibase.api_host</code> (optional)
 *
 * This caches certain Communibase API calls; reducing network calls. Default cache expiration is a half
 * hour (this can be overridden).
 *
 * In a DEVELOPMENT environment there is no caching; due to the expected API URI to also be a development version.
 * You can also use the configured Connector directly via <code>getConnector()</code>
 *
 * All wrapped updates reset the cache (or re-cache); keeping a consistent cache.
 *
 * Wrapped (Caching) methods (wrapped by common usage):
 *  search     - cached by entityType/query/params
 *  getById   - cached by entityType/id/params
 *  update     - cached by entityType/id/params (on successful update)
 *  getBinary   - cached by entityType/id
 *  updateBinary - cached by entityType/id (on successful update)
 *
 * All other unwrapped methods are directly available (via __call magic).
 *
 * DEV note: List unwrapped/magic methods here for IDE completion (if ever wrapped then remove the method):
 * (@see http://phpdoc.org/docs/latest/references/phpdoc/tags/method.html)
 *
 */
class WP_Communibase_Connector implements \Communibase\ConnectorInterface
{

  /**
   * Get a singleton instance of this based on the key
   *
   * @param string|null $key
   *
   * @return \WP_Communibase_Connector
   *
   * @throws \Communibase\Exception
   */
  public static function getInstance($key = null)
  {
    static $connectors = [];
    if ( ! array_key_exists($key, $connectors)) {
      $connectors[$key] = new self($key);
    }

    return $connectors[$key];
  }

  /**
   * @var Connector
   */
  private $connector;

  /**
   * Holds the configuration for easier use within this class; externally retrieve keys via getConfig()
   *
   * @var array
   */
  private $options = [];

  /**
   * Create a new WP_Communibase_Connector based on the given key, else will use key from plugin settings or from env.
   *
   * @param string|null $key
   *
   * @throws \Communibase\Exception
   */
  public function __construct($key = null)
  {
    // possibly have communibase options from wordpress plugin
    $this->options = get_option('communibase');
    if ($this->options === null) {
      $this->options = [];
    }

    if ($key !== null) {
      $this->options['api_key'] = $key;
    }

    $key = $this->getOption('api_key', getenv('COMMUNIBASE_KEY'), true);

    $url = $this->getOption('api_url', [getenv('COMMUNIBASE_URL'), Connector::SERVICE_PRODUCTION_URL], true);

    if ($url === Connector::SERVICE_PRODUCTION_URL && getenv('PHP_ENV') === 'development') {
      // TODO throw an Exception instead of using internal servers.. i.e. force a dev user to set a dev url...
      $url = 'http://communibase.plunger.kingsquare.eu/0.1/';
    }

    $this->connector = $this->createConnector($key, $url);

    $host = $this->getOption('api_host', getenv('COMMUNIBASE_HOST'), false);
    if ($host) {
      $this->connector->addExtraHeaders(['Host' => $host]);
    }

    // add logger if required; @todo possible use db.log_queries for this as well?
    if (getenv('PHP_ENV') === 'development') {
      $this->connector->setQueryLogger(new \Communibase\Logging\DebugStack());
    }
  }

  /**
   * Get a plugin option/setting (or all if the $key is empty)
   *
   * @param string $key The option key
   * @param mixed $defaultValue If the key does not exist use this value (can be an array which will be reduced to the first non empty value)
   * @param bool $required Throw an exception if the key is required and no default is given
   *
   * @return mixed
   *
   * @throws \Communibase\Exception
   */
  protected function getOption($key, $defaultValue = null, $required = true)
  {
    if ( ! $key) {
      return $this->options;
    }

    if ($this->options !== null && array_key_exists($key, $this->options) &&  !empty($this->options[$key])) {
      return $this->options[$key];
    }

    if ($defaultValue !== null) {
      if (is_array($defaultValue)) {
        return array_reduce($defaultValue, function ($carry, $item) {
          if (empty($carry) && ! empty($item)) {
            $carry = $item;
          }

          return $carry;
        }, null);
      }

      return $defaultValue;
    }

    if ($required) {
      throw new \Communibase\Exception('Can not find setting for "' . $key . '""');
    }

    return null;
  }

  /**
   *
   */
  protected function createConnector($key, $url)
  {
    return new \Communibase\Connector($key, $url);
  }

  /**
   * Get the actual Connector and do stuff on it. Using this bypasses all caching.
   *
   * @return Connector
   */
  public function getConnector()
  {
    return $this->connector;
  }

  /**
   * Default cache expiration is 15 mins. If is development; there is no cache
   *
   * NOTE: override this for faster cache refresh
   *
   * @return int|bool if false does not cache
   */
  protected function getCacheTimeout()
  {
    if (getenv('PHP_ENV') === 'development') {
      return false;
    }

    // https://codex.wordpress.org/Transients_API#Using_Time_Constants
    return MINUTE_IN_SECONDS * 15;
  }

  /////////////// BELOW ARE OVERRIDDEN CONNECTOR METHODS THAT IMPLEMENT CACHING

  /**
   * Search the API
   *
   * @param string $entityType
   * @param array $query
   * @param array $params
   *
   * @return array|mixed|null
   *
   * @throws \Communibase\Exception
   */
  public function search($entityType, array $query = [], array $params = [])
  {
    $expiration = $this->getCacheTimeout();
    if ($expiration === false) {
      return $this->getConnector()->search($entityType, $query, $params);
    }

    $identifier = md5(json_encode([__METHOD__, func_get_args()]));

    $data = get_transient($identifier);
    if ($data === false) {
      $data = $this->getConnector()->search($entityType, $query, $params);
      set_transient($identifier, $data, $expiration);
    }

    // crappy hack? (copied from kmt)
    if (empty($data)) {
      return [];
    }

    return $data;
  }


  /**
   *
   * @param string $entityType
   * @param array $query
   * @param array $params
   *
   * @return array|mixed|null
   *
   * @throws \Communibase\Exception
   */
  public function getIds($entityType, array $query = [], array $params = [])
  {
    $expiration = $this->getCacheTimeout();

    if ($expiration === false) {
      return $this->getConnector()->getIds($entityType, $query, $params);
    }

    $identifier = md5(json_encode([__METHOD__, func_get_args()]));

    $data = get_transient($identifier);
    if ($data === null) {
      $data = $this->getConnector()->getIds($entityType, $query, $params);
      set_transient($identifier, $data, $expiration);
    }

    // crappy hack? (copied from kmt)
    if (empty($data)) {
      return [];
    }

    return $data;
  }

  /**
   * @param $entityType
   * @param $id
   *
   * @return bool
   *
   * @throws \Communibase\Exception
   */
  public function isIdA($entityType, $id)
  {
    static $_cache = [];
    if ( ! isset($_cache[$id][$entityType])) {
      $entity = $this->search($entityType, ['_id' => $id], ['fields' => ['_id']]);
      $_cache[$id][$entityType] = ! empty($entity);
    }

    return $_cache[$id][$entityType];
  }

  /**
   * @param string $entityType
   * @param string $id
   * @param array $params
   * @param string|null $version
   *
   * @return array|mixed|null
   *
   * @throws Exception
   */
  public function getById($entityType, $id, array $params = [], $version = null)
  {
    $identifier = $this->getByIdIdentifierCache($entityType, $id, $params);
    if ( ! $identifier) {
      return $this->getConnector()->getById($entityType, $id, $params, $version);
    }

    $data = get_transient($identifier);
    if ($data === false) {
      $data = $this->getConnector()->getById($entityType, $id, $params, $version);
      set_transient($identifier, $data, $this->getCacheTimeout());
    }

    return $data;
  }

  /**
   * Updates/Creates the given Entity
   *
   * After Update/Create the Entity will be cached
   *
   * @param string $entityType
   * @param array $params
   *
   * @return array|mixed|null
   *
   * @throws \Communibase\Exception
   */
  public function update($entityType, array $params = [])
  {
    $data = $this->getConnector()->update($entityType, $params);
    if (empty($data)) {
      return [];
    }

    $identifier = $this->getByIdIdentifierCache($entityType, $data['_id'], []);
    if ($identifier) {
      set_transient($identifier, $data, $this->getCacheTimeout());
    }

    return $data;
  }

  /**
   * Since the cache should be object specific this identifier is reused twice and needs to be _exactly_ the same.
   *
   * @param $entityType
   * @param $id
   * @param array $params
   *
   * @return string
   */
  private function getByIdIdentifierCache($entityType, $id, $params)
  {
    $expiration = $this->getCacheTimeout();
    if ($expiration === false) {
      return null;
    }

    return md5(json_encode([__METHOD__, func_get_args()]));
  }

  /**
   * @override As Connector::getBinary does not take an optional host into account (uses file_get_contents)
   * @override To temporarily cache data
   *
   * Get the binary contents of a file by its ID
   *
   * NOTE: for meta-data like filesize and mimetype, one can use the getById()-method.
   *
   * @param string $id id string for the file-entity
   *
   * @return string Binary contents of the file.
   *
   * @throws \Communibase\Exception
   */
  public function getBinary($id)
  {
    $identifier = $this->getFileIdentifierCache($id);

    if ($identifier) {
      $data = get_transient($identifier);
      if ($data !== false) {
        return $data;
      }
    }

      /** @noinspection SuspiciousAssignmentsInspection */
      $data = (string)$this->connector->getBinary($id);

    if (empty($data) || ! empty($data['message'])) {
      return $data;
    }

    if ($identifier) {
      set_transient($identifier, $data, $this->getCacheTimeout());
    }

    return $data;
  }

  /**
   * Updates/Creates a new binary File
   *
   * @param \Psr\Http\Message\StreamInterface $resource
   * @param string $name
   * @param string $destinationPath
   * @param string $id
   *
   * @return array|null JSON result as an array
   *
   * @throws \Exception
   */
  public function updateBinary(\Psr\Http\Message\StreamInterface $resource, $name, $destinationPath, $id = '')
  {

    $data       = $this->connector->updateBinary($resource, $name, $destinationPath, $id);
    $identifier = $this->getFileIdentifierCache($data['_id']);
    if ($identifier) {
      // read file to cache the binary data...
      $resource->rewind();
      set_transient($identifier, $resource->getContents(), $this->getCacheTimeout());
    }

    return $data;
  }

  /**
   * Since the cache should be file specific this identifier is reused twice and needs to be _exactly_ the same.
   *
   * @param $id
   *
   * @return string|null
   */
  private function getFileIdentifierCache($id)
  {
    $expiration = $this->getCacheTimeout();
    if ($expiration === false) {
      return null;
    }

    return md5(json_encode([__METHOD__, func_get_args()]));
  }

  /**
   * MAGIC for delegating un-wrapped/non-cached methods to the Connector; making this a viable Connector alternative.
   *
   * @param string $name
   * @param array $arguments
   *
   * @return mixed
   *
   * @throws \BadMethodCallException If the method can not be delegated
   */
  public function __call($name, $arguments)
  {
    if (is_callable([$this->connector, $name])) {
      return call_user_func_array([$this->connector, $name], $arguments);
    }
    throw new \BadMethodCallException('Method "' . $name . '" does not exist.');
  }

  /**
   * Returns an array that has all the fields according to the definition in Communibase.
   *
   * @param string $entityType
   *
   * @return array
   *
   * @throws Exception
   */
  public function getTemplate($entityType)
  {
    return $this->connector->getTemplate($entityType);
  }

  /**
   * Get a single Entity by a ref-string
   *
   * @param array $ref
   * @param array $parentEntity (optional)
   *
   * @return array the referred Entity data
   *
   * @throws Exception
   */
  public function getByRef(array $ref, array $parentEntity = [])
  {
    return $this->connector->getByRef($ref, $parentEntity);
  }

  /**
   * Get an array of entities by their ids
   *
   * @param string $entityType
   * @param array $ids
   * @param array $params (optional)
   *
   * @return array entities
   *
   * @throws Exception
   */
  public function getByIds($entityType, array $ids, array $params = [])
  {
    return $this->connector->getByIds($entityType, $ids, $params);
  }

  /**
   * Get all entities of a certain type
   *
   * @param string $entityType
   * @param array $params (optional)
   *
   * @return array|null
   *
   * @throws Exception
   */
  public function getAll($entityType, array $params = [])
  {
    return $this->connector->getAll($entityType, $params);
  }

  /**
   * Get the id of an entity based on a search
   *
   * @param string $entityType i.e. Person
   * @param array $selector (optional) i.e. ['firstName' => 'Henk']
   *
   * @return array resultData
   */
  public function getId($entityType, array $selector = [])
  {
    return $this->connector->getId($entityType, $selector);
  }

  /**
   * @inheritdoc
   */
  public function aggregate($entityType, array $pipeline)
  {
    $expiration = $this->getCacheTimeout();
    if ($expiration === false) {
      return $this->getConnector()->aggregate($entityType, $pipeline);
    }

    $identifier = md5(json_encode([__METHOD__, func_get_args()]));

    $data = get_transient($identifier);
    if ($data === false) {
      $data = $this->getConnector()->aggregate($entityType, $pipeline);
      set_transient($identifier, $data, $expiration);
    }
    if (empty($data)) {
      return [];
    }

    return $data;
  }

  /**
   * Returns an array of the history for the entity with the following format:
   *
   * <code>
   *  [
   *        [
   *            'updatedBy' => '', // name of the user
   *            'updatedAt' => '', // a string according to the DateTime::ISO8601 format
   *            '_id' => '', // the ID of the entity which can ge fetched seperately
   *        ],
   *        ...
   * ]
   * </code>
   *
   * @param string $entityType
   * @param string $id
   *
   * @return array
   *
   * @throws Exception
   */
  public function getHistory($entityType, $id)
  {
    return $this->connector->getHistory($entityType, $id);
  }

  /**
   * Finalize an invoice by adding an invoiceNumber to it.
   * Besides, invoice items will receive a "generalLedgerAccountNumber".
   * This number will be unique and sequential within the "daybook" of the invoice.
   *
   * NOTE: this is Invoice specific
   *
   * @param string $entityType
   * @param string $id
   *
   * @return array
   *
   * @throws Exception
   */
  public function finalize($entityType, $id)
  {
    return $this->connector->finalize($entityType, $id);
  }

  /**
   * Delete something from Communibase
   *
   * @param string $entityType
   * @param string $id
   *
   * @return array resultData
   *
   * @throws Exception
   */
  public function destroy($entityType, $id)
  {
    return $this->connector->destroy($entityType, $id);
  }

  /**
   * @param string $id
   *
   * @return bool
   */
  public function isIdValid($id)
  {
    return Connector::isIdValid($id);
  }

  /**
   * Generate a Communibase compatible ID, that consists of:
   *
   * a 4-byte timestamp,
   * a 3-byte machine identifier,
   * a 2-byte process id, and
   * a 3-byte counter, starting with a random value.
   *
   * @return string
   */
  public function generateId()
  {
    return Connector::generateId();
  }

  /**
   * Add extra headers to be added to each request
   *
   * @see http://php.net/manual/en/function.header.php
   *
   * @param array $extraHeaders
   */
  public function addExtraHeaders(array $extraHeaders)
  {
    $this->connector->addExtraHeaders($extraHeaders);
  }

  /**
   * @param \Communibase\Logging\QueryLogger $logger
   */
  public function setQueryLogger(\Communibase\Logging\QueryLogger $logger)
  {
    $this->connector->setQueryLogger($logger);
  }

  /**
   * @return \Communibase\Logging\QueryLogger
   */
  public function getQueryLogger()
  {
    return $this->connector->getQueryLogger();
  }

  /**
   * @param array $data
   *
   * @return string
   */
  public function generateSalutation(array $data)
  {
    $salutation = [
      'Geachte'
    ];
    $gender     = 'heer of mevrouw';
    if ( ! empty($data['gender'])) {
      $gender = ($data['gender'] === 'M') ? 'heer' : 'mevrouw';
    }
    $salutation[] = $gender;
    if ( ! empty($data['middleName'])) {
      $salutation[] = ucwords($data['middleName']);
    }
    if ( ! empty($data['maidenName'])) {
      $data['lastName'] .= '-' . $data['maidenName'];
    }
    $salutation[] = $data['lastName'];

    return trim(implode(' ', $salutation)) . ',';
  }

}
