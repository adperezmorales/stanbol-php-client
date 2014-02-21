<?php

namespace Stanbol\Services\Entityhub;

use Stanbol\Http\MediaType;
use Stanbol\Client\Exception\StanbolClientHttpException;
use Stanbol\Services\AbstractStanbolService;
use Stanbol\Services\Enhancer\Model\Parser\EnhancementsParserFactory;
use Stanbol\Services\Entityhub\StanbolEntityhubService;
use Stanbol\Services\Entityhub\Model\LDPathProgram;
use Stanbol\Services\Exception\StanbolServiceException;
use Stanbol\Util\FormatHelper;
use Stanbol\Vocabulary\Model\Entity;
use Stanbol\Vocabulary\Model\Serialiser\RdfXmlEntitySerialiser;

/**
 * <p>Class representing the implementation of <code>StanbolEnhancerService</code></p>
 *
 * @see Stanbol\Services\StanbolEnhancerService
 * 
 * @author Antonio David Perez Morales <adperezmorales@gmail.com>
 */
class StanbolEntityhubServiceImpl extends AbstractStanbolService implements StanbolEntityhubService
{

    /**
     * <p>Entity Path constant</p>
     * @var string
     */
    private static $ENTITY_PATH = 'entity';

    /**
     * <p>Lookup Path constant</p>
     * @var string
     */
    private static $LOOKUP_PATH = 'lookup';

    /**
     * <p>Find Path constant</p>
     * @var string
     */
    private static $FIND_PATH = 'find';

    /**
     * <p>LDPath Path constant</p>
     * @var string
     */
    private static $LDPATH_PATH = 'ldpath';

    /**
     * <p>Id Parameter</p>
     * @var string
     */
    private static $ID_PARAM = 'id';

    /**
     * <p>Create Parameter</p>
     * @var string
     */
    private static $CREATE_PARAM = 'create';

    /**
     * <p>Update Parameter</p>
     * @var string
     */
    private static $UPDATE_PARAM = 'update';

    /**
     * <p>The Stanbol Entityhub site to use for storing, retrieve and looking for entities</p>
     * @var string 
     */
    private $site;

    /**
     * {@inheritdoc}
     */
    public function setSite($site)
    {
        assert('is_string($site)');
        $this->site = $site;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * {@inheritdoc}
     */
    public function setLocalSite()
    {
        $this->site = '';
        return $this;
    }

    /**
     * {inheritdoc}
     */
    public function getReferencedSites()
    {
        /*
         * The httpClient already contains the Stanbol Endpoint, so it is only needed to add the required Entityhub part
         */
        $request = $this->httpClient->get(self::ENTITYHUB_SITEMANAGER_PATH . "referenced")->send();

        $response = $this->executeRequest($request);

        $referencedSites = array();

        $referencedSitesString = $response->getBody(true);

        $refSites = json_decode($referencedSitesString, true);

        if ($refSites === NULL) {
            throw new StanbolServiceException("Malformed JSON response for EntityHub referenced service");
        }

        foreach ($refSites as $referencedSite) {
            if (filter_var(FILTER_VALIDATE_URL, $referencedSite) === FALSE) {
                throw new StanbolServiceException("Bad ReferencedSite URL " . $referencesSite);
            }

            array_push($referencedSites, $referencedSite);
        }

        return $referencedSites;
    }

    /**
     * {@inheritdoc}
     */
    public function create(Entity $entity, $update = false)
    {
        assert('!is_null($entity)');
        if (is_null($entity)) {
            throw new \InvalidArgumentException('The entity to be created can not be null');
        }

        $rdfXmlEntitySerializer = new RdfXmlEntitySerialiser();
        $serializedContent = $rdfXmlEntitySerializer->serialize($entity);

        return $this->_create($entity->getUri(), $serializedContent, $update);
    }

    /**
     * {@inheritdoc}
     */
    public function createFromFile($file, $id = null, $update = false)
    {
        assert('file_exists($file)');
        if (!file_exists($file) || !is_readable($file))
            throw new \InvalidArgumentException('The file ' . $file . ' does not exist or it is not readable');

        if (FormatHelper::guessFormat($model, $file) != MediaType::APPLICATION_RDF_XML)
            throw new \InvalidArgumentException('The file ' . $file . ' is not a RDF-format file');
        
        $model = file_get_contents($file);

        return $this->_create($id, $model, $update);
    }

    /**
     * <p>Auxiliar function to create the entity in Stanbol Entityhub</p>
     * 
     * @param string $id The id of the entity to be created
     * @param string $entityContent The entity in RDF/XML string format
     * @param string $update flag to update the entity if it already exists. Default is false
     * @return The created entity URI
     */
    private function _create($id, $entityContent, $update = false)
    {
        $updateParam = (bool) $update;
        $updateParam = $updateParam === false ? 'false' : 'true';

        $parameters = array(self::$UPDATE_PARAM => $updateParam);
        if ($id != null)
            $parameters[self::$ID_PARAM] = $id;

        $entityhubEndpoint = $this->buildEntityhubUrlPath(self::$ENTITY_PATH, $parameters, $this->site);

        $request = $this->httpClient->post($entityhubEndpoint, array(self::CONTENT_TYPE_HEADER => MediaType::APPLICATION_RDF_XML), $entityContent);
        $response = $request->send();

        if ($response->getStatusCode() == 400) {
            throw new StanbolServiceException('Entity being created already exists within Entityhub. You migh want to pass update param with a true value');
        }

        if ($response->isError()) {
            throw new StanbolServiceException('[HTTP ' . $response->getStatusCode() . '] Error while posting content into Stanbol Entityhub');
        }

        return substr($response->getLocation(), strrpos($response->getLocation(), self::$ID_PARAM . "=") + strlen(self::$ID_PARAM) + 1);
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        assert('!is_null($id)');

        if (is_null($id) || empty($id))
            throw new \InvalidArgumentException("The id of the entity to retrieve can not be null or empty");

        $parameters = array(self::$ID_PARAM => $id);

        $entityhubEndpoint = $this->buildEntityhubUrlPath(self::$ENTITY_PATH, $parameters, $this->site);

        $request = $this->httpClient->get($entityhubEndpoint, array(self::ACCEPT_HEADER => MediaType::APPLICATION_RDF_XML));
        $response = $request->send();

        if ($response->getStatusCode() == 404)
            return null;

        else {
            $model = $response->getBody(true);
            return $this->parseEntity($id, $model);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function update(Entity $entity, $create = false)
    {
        assert('!is_null($entity)');
        if (is_null($entity)) {
            throw new \InvalidArgumentException('The entity to be updated can not be null');
        }

        $rdfXmlEntitySerializer = new RdfXmlEntitySerialiser();
        $serializedContent = $rdfXmlEntitySerializer->serialize($entity);

        return $this->_update($entity->getUri(), $serializedContent, $create);
    }

    /**
     * {@inheritdoc}
     */
    public function updateFromFile($file, $id = null, $create = false)
    {
        assert('file_exists($file)');
        if (!file_exists($file) || !is_readable($file))
            throw new \InvalidArgumentException('The file ' . $file . ' does not exist or it is not readable');

        $model = file_get_contents($file);

        if (FormatHelper::guessFormat($model, $file) != MediaType::APPLICATION_RDF_XML)
            throw new \InvalidArgumentException('The file ' . $file . ' is not a RDF-format file');

        return $this->_update($id, $model, $create);
    }

    /**
     * <p>Auxiliar function to create the entity in Stanbol Entityhub</p>
     * 
     * @param string $id The id of the entity to be created
     * @param string $entityContent The entity in RDF/XML string format
     * @param string $update flag to update the entity if it already exists
     * @return The updated entity URI
     */
    private function _update($id, $entityContent, $create)
    {
        $createParam = (bool) $create;
        $createParam = $createParam === false ? 'false' : 'true';

        $parameters = array(self::$CREATE_PARAM => $createParam);
        if ($id != null)
            $parameters[self::$ID_PARAM] = $id;

        $entityhubEndpoint = $this->buildEntityhubUrlPath(self::$ENTITY_PATH, $parameters, $this->site);

        $request = $this->httpClient->put($entityhubEndpoint, array(self::CONTENT_TYPE_HEADER => MediaType::APPLICATION_RDF_XML, self::ACCEPT_HEADER => MediaType::APPLICATION_RDF_XML), $entityContent);
        $response = $request->send();

        if ($response->getStatusCode() == 400) {
            throw new StanbolServiceException('Entity being updated does not exist within Entityhub. You migh want to pass create param with a true value');
        }

        if ($response->isError()) {
            throw new StanbolServiceException('[HTTP ' . $response->getStatusCode() . '] Error while putting content into Stanbol Entityhub');
        }

        //Log
        /* "Content " . $id . " has been sucessfully updated at " . $response->getLocation() */

        return $this->parseEntity($id, $response->getBody(true));
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {

        assert('!is_null($id)');

        if (\is_null($id) || empty($id))
            throw new \InvalidArgumentException("The id of the entity to retrieve can not be null or empty");

        $parameters = array(self::$ID_PARAM => $id);

        $entityhubEndpoint = $this->buildEntityhubUrlPath(self::$ENTITY_PATH, $parameters, $this->site);

        $request = $this->httpClient->delete($entityhubEndpoint);
        $response = $request->send();

        if ($response->getStatusCode() == 404)
            return false;

        if ($response->isError()) {
            throw new StanbolServiceException('[HTTP ' . $response->getStatusCode() . '] Error while deleting content from Stanbol Entityhub');
        }

        //Log
        /* "Content " . $id . " has been sucessfully deleted at " . $response->getLocation() */
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function lookup($id, $create = false)
    {
        assert('!is_null($id) && !empty($id)');

        if (\is_null($id) || empty($id))
            throw new \InvalidArgumentException("The id of the entity to retrieve can not be null or empty");

        $createParam = (bool) $create;
        $createParam = $createParam === false ? 'false' : 'true';

        $parameters = array(self::$ID_PARAM => $id, self::$CREATE_PARAM => $create);

        $entityhubEndpoint = $this->buildEntityhubUrlPath(self::$LOOKUP_PATH, $parameters);

        $request = $this->httpClient->get($entityhubEndpoint, array(self::ACCEPT_HEADER => MediaType::APPLICATION_RDF_XML));
        $response = $request->send();

        if ($response->getStatusCode() == 404)
            return null;

        if ($response->getStatusCode() == 403)
            throw new StanbolServiceException(
                    "Creation of new Symbols is not allowed in the current Stanbol Configuration");

        if ($response->isError()) {
            throw new StanbolServiceException("[HTTP " . $response->getStatusCode() . "] Error retrieving content from stanbol server");
        }

        //Log
        /* "Entity " . $id . " has been sucessfully looked up from " . $response->getLocation() */

        return $this->parseEntity($id, $response->getBody(true));
    }

    /**
     * {@inheritdoc}
     */
    public function find($name, $field = null, $language = null, $ldpath = null, $limit = null, $offset = 0)
    {
        assert('!is_null($name) && !empty($name)');
        if (\is_null($name) || empty($name))
            throw new \InvalidArgumentException("The name of the entity can not be null nor empty");
        
        $parameters = array();
        $parameters['name'] = $name;

        if ($field != null && !empty($field))
            $parameters['field'] = $field;
        if ($language != null && !empty($language))
            $parameters['language'] = $language;

        if ($ldpath != null && $ldpath instanceof LDPathProgram)
            $parameters['ldpath'] = urlencode($ldpath->__toString());
        
        if ($ldpath != null && !empty($ldpath))
            $parameters['ldpath'] = urlencode($ldpath);

        if ($limit != null)
            $parameters['limit'] = $limit;

        $parameters['offset'] = $offset;

        $entityhubEndpoint = $this->buildEntityhubUrlPath(self::$FIND_PATH, $parameters, $this->site);

        $request = $this->httpClient->get($entityhubEndpoint, array(self::ACCEPT_HEADER => MediaType::APPLICATION_JSON));
        $response = $request->send();

        // Check HTTP status code

        if ($response->isError()) {
            throw new StanbolServiceException("[HTTP " . $response->getStatusCode() . "] Error retrieving content from stanbol server");
        }

        //Log
        /* "Entities by " . $parameters["name"] . " has been found sucessfully " . $response->getLocation() */

        $jsonModel = $response->getBody(true);
        $modelArray = json_decode($jsonModel, true);

        $results = $modelArray['results'];

        return $this->parseFindResults($results);
    }

    /**
     * {@inheritdoc}
     */
    public function ldpath($contexts, $ldPathProgram = null)
    {
        assert('(!is_null($contexts) && !empty($contexts)) || (is_array($contexts) && count($contexts) > 0)');
        assert('!is_null($ldPathProgram)');

        if ($contexts == null || empty($contexts) || count($contexts) == 0)
            throw new \InvalidArgumentException('No context was provided by the Request. Missing parameter context');

        if ($ldPathProgram == null || empty($ldPathProgram))
            throw new \InvalidArgumentException('No ldpath program was provided by the Request. Missing or empty parameter ldpath.');

        $parameters = array();
        $parameters['context'] = $contexts;
        $parameters['ldpath'] = $ldPathProgram instanceof LDPathProgram ? $ldPathProgram->__toString() : $ldPathProgram;

        $entityhubEndpoint = $this->buildEntityhubUrlPath(self::$LDPATH_PATH, $parameters, $this->site);

        $request = $this->httpClient->get($entityhubEndpoint, array(self::ACCEPT_HEADER => MediaType::APPLICATION_RDF_JSON));
        $response = $request->send();

        // Check HTTP status code
        if ($response->isError()) {
            throw new StanbolServiceException("[HTTP " . $response->getStatusCode() . "] Error retrieving content from stanbol server");
        }


        //Log
        /* "LDPath Program " . $ldPathProgram . " executed sucessfully in " . $response->getLocation() . " over the context " . $contexts) */

        $response = $response->getBody(true);
        $response = json_decode($response, true);
        
        $result = array();
        foreach($response as $subject => $values) {
            foreach($values as $field => $fieldValues) {
                foreach($fieldValues as $fieldValue) {
                    $result[$subject][$field][] = $fieldValue['value'];
                }
            }
        }
        
        return $result;
    }

    /**
     * <p>Constructs the path for the entityhub to be called using the client, using the entity path, the site passed by parameter (or the selected site if selected) and the given parameters</p>
     */
    private function buildEntityhubUrlPath($path, $params = array(), $site = null)
    {
        $referencedSite = $site != null ? $site : '';
        $entityhubEndpoint = empty($referencedSite) ? self::ENTITYHUB_URL_PATH : self::ENTITYHUB_URL_SITE_PATH . $referencedSite . '/';
        $entityhubEndpoint .= $path;

        $queryParams = '';
        foreach ($params as $parameter => $value) {
            if (is_array($value)) {
                foreach ($value as $val) {
                    $queryParams .= $parameter . '=' . urlencode($value) . '&';
                }
            } elseif ($value != null)
                $queryParams .= $parameter . '=' . urlencode($value) . '&';
        }

        $queryParams = trim($queryParams, '&');
        if (!empty($queryParams))
            $entityhubEndpoint .='?' . $queryParams;

        return $entityhubEndpoint;
    }

    /**
     * <p>Parse an entity identified by the entity uri into \Stanbol\Vocabulary\Model\Entity</p>
     * 
     * @param string $id the entity uri to parse
     * 
     * @return \Stanbol\Vocabulary\Model\Entity the parsed entity
     */
    private function parseEntity($id, $entityModel)
    {
        $enhancementsParser = EnhancementsParserFactory::createDefaultParser($entityModel);
        return $enhancementsParser->parseEntity($id);
    }

    /**
     * <p>Parse the entities found through the find endpoint</p>
     * 
     * @param array $findResults The array of results
     */
    private function parseFindResults($findResults)
    {
        $entities = array();

        foreach ($findResults as $result) {
            $entity = new Entity();
            foreach ($result as $property => $values) {
                if ($property == 'id') {
                    $entity->setUri($values);
                } else {
                    foreach ($values as $value) {
                        $entity->addPropertyValue($property, $value['value'], isset($value['xml:lang']) ? $value['xml:lang'] : null);
                    }
                }
            }

            array_push($entities, $entity);
        }

        return $entities;
    }

}

?>
