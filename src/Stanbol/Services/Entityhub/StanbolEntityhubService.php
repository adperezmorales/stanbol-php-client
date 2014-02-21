<?php

namespace Stanbol\Services\Entityhub;

use Stanbol\Services\Exception\StanbolServiceException;
use Stanbol\Services\Entityhub\Model\LDPathProgram;
use Stanbol\Vocabulary\Model\Entity;

/**
 * <p>Stanbol Entityhub Service</p>
 * <p>The Entityhub provide two main services. First it allows to manage a network of site used to consume Entity Information from 
 *  and second it allows to manage locally used Entities</p>
 * 
 * @author Antonio David Perez Morales <adperezmorales@gmail.com>
 */
interface StanbolEntityhubService
{
    /**
     * <p>Entityhub URL Path containing the path to access the Entityhub</p>
     */
    const ENTITYHUB_URL_PATH = "entityhub/";

    /**
     * <p>Entityhub URL Site Path containing the path to access a specific Referenced Site</p>
     */
    const ENTITYHUB_URL_SITE_PATH = "entityhub/site/";

    /**
     * 
     */
    const ENTITYHUB_SITEMANAGER_PATH = "entityhub/sites/";

    /**
     * <p>Gets the Entityhub Site being used</p>
     */
    public function getSite();

    /**
     * <p>Sets the Entityhub Site to be used (Fluent API)</p>
     * 
     * @param string $site The site to be used
     * @return StanbolEntityhubService The current instance
     */
    public function setSite($site);

    /**
     * <p>Sets the Entityhub local site to be used (Fluent API)</p>
     * <p>It sets the whole Entityhub site to be used</p>
     */
    public function setLocalSite();

    /**
     * <p>Returns a list containing the IDs of all referenced sites configured in Stanbol</p>
     *
     * @return List of sites URLs
     * @throws \Stanbol\Services\Exception\StanbolServiceException
     */
    public function getReferencedSites();

    /**
     * <p>Creates entities for the EntityHub. If any of such Entities already exists within the Entityhub and the update parameter
     * is false, a {@link StanbolServiceException} will be thrown</p>
     *
     * @param string $file The name of the file containing entities in RDF format. If the file doesn't exist an IllegalArgumentException is thrown
     * @param string $id The URI of the entity. If the URI is null, RDF entities' ids will be used. If the URI is not null, only the
     * referenced entity will be created or updated
     * @param boolean $update If true, entities that already exist will be updated. Default is false
     * @return The URI of the created Entity
     * @throws InvalidArgumentException
     * @throws StanbolServiceException
     */
    public function createFromFile($file, $id = null, $update= false);

    /**
     * <p>Creates an entity in the EntityHub. If the Entity already exists within the Entityhub and the update parameter
     * is false, a {@link StanbolServiceException} will be thrown</p>
     *
     * @param \Stanbol\Vocabulary\Model\Entity The Entity to be created
     * @param boolean $update If true and the Entity already exists within the EntityHub, the Entity will be updated. Default is false
     * @return The URI of the created Entity
     * @throws InvalidArgumentException
     * @throws StanbolServiceException
     */
    public function create(Entity $entity, $update = false);

    /**
     * <p>This service searches in the configured referenced site for the entity with the passed URI.
     * <p>If no site is configured or the site doesn't exist as ReferencedSite, the service would search the entity over
     * all referenced sites configured in Stanbol. If the requested entity can not be found a null object is returned.</p>
     *
     * @param string $id Entity's URI
     * @return \Stanbol\Vocabulary\Model\Entity {@link Entity}
     * @throws InvalidArgumentException
     * @throws StanbolServiceException
     */
    public function get($id);

    /**
     * <p>Update entities for the EntityHub. If any of such Entities doesn't exist within the Entityhub and create parameter is
     * false, a {@link StanbolServiceException} will be thrown</p>
     *
     * @param \Stanbol\Vocabulary\Model\Entity $entity Entity to be updated
     * @param boolean $create If true and the Entity doesn't exist within the EntityHub, the Entity will be created. Default is true
     * @return mixed The data of the entity as <code>Entity</code>
     * @throws InvalidArgumentException
     * @throws StanbolServiceException
     */
    public function update(Entity $entity, $create = false);

    /**
     * <p>Update entities for the EntityHub. If any of such Entities doesn't exist within the Entityhub and create parameter is
     * false, a {@link StanbolServiceException} will be thrown</p>
     *
     * @param string $file The name of the file containing entities in RDF format. If the file doesn't exist an IllegalArgumentException is thrown
     * @param string $id The URI of the entity. If the URI is null, RDF entities' ids will be used. If the URI is not null, only the
     * referenced entity will be created or updated
     * @param boolean $create If true and the Entity doesn't exist within the EntityHub, the Entity will be created. Default is true
     * @return mixed The data of the entity as <code>Entity</code>
     * @throws StanbolServiceException
     * @throws InvalidArgumentException
     */
    public function updateFromFile($file, $id = null, $create = false);

    /**
     * <p>Delete an entity managed by the Entityhub by its URI</p>
     *
     * @param string $id URI of the Entity to delete
     * @return boolean true if the Entity has been successfully deleted and false if the entity is not found
     * @throws InvalidArgumentException
     * @throws StanbolServiceException
     */
    public function delete($id);

    /**
     * <p>This service looks-up Symbols (Entities managed by the Entityhub) based on the passed URI. The passed ID can be
     * the URI of a Symbol or an Entity of any referenced site.</p>
     *
     * <p>This service looks-up Symbols (Entities managed by the Entityhub) based on the parsed URI. The parsed ID can be the URI of a Symbol or an Entity of any referenced site.
     * 
     *  <ul>
     *      <li>If the parsed ID is a URI of a Symbol, than the stored information of the Symbol are returned in the requested media type ('accept' header field).</li>
     *      <li>If the parsed ID is a URI of an already mapped entity, then the existing mapping is used to get the according Symbol.</li>
     *      <li>If "create" is enabled, and the parsed URI is not already mapped to a Symbol, than all the currently active referenced sites are searched for an Entity with the parsed URI.</li>
     *      <li>If the configuration of the referenced site allows to create new symbols, than a the entity is imported in the Entityhub, a new Symbol and EntityMapping is created and the newly created Symbol is returned.</li>
     *      <li>In case the entity is not found (this also includes if the entity would be available via a referenced site, but create=false) a 404 "Not Found" is returned.</li>
     *      <li>In case the entity is found on a referenced site, but the creation of a new Symbol is not allowed a {@link StanbolServiceException} is thrown.</li>
     * </ul>
     * </p>
     * <p>This method does not use any site but the whole entityhub site to store the created entity if it is not contained in the entityhub site and create parameter is enabled</p>
     *
     * @param string $id URI of the Entity/Symbol/ReferencedSite
     * @param boolean $create If true, a new symbol is created if necessary and allowed. False by default
     * @return The Entity
     * 
     * @throws InvalidArgumentException
     * @throws StanbolServiceException
     */
    public function lookup($id, $create = false);

    /**
     * <p>Find ReferencedSite managed Entities by label based search</p>
     *  <p>The site used to perform the search is the one configured through setSite and setLocalSite methods (default is whole Entityhub)</p>
     *
     * @param string $name The name of the Entity to search. Supports '*' and '?
     * @param string $field The name of the field to search the name. Optional, default is rdfs:label
     * @param string $language The language of the parsed name (default: any)
     * @param mixed $ldpath The LDPath program to execute. It can be a string containing the LDPath program or an instance of \Stanbol\Services\Entityhub\Model\LDPathProgram
     * @param int $limit The maximum number of returned Entities (optional)
     * @param int $offset The offset of the first returned Entity (default: 0)
     * @return an array of found entities
     * @throws InvalidArgumentException
     * @throws StanbolServiceException
     */
    public function find($name, $field = null, $language = null, $ldpath = null, $limit = null, $offset = 0);

    /**
     * <p>Allows to execute an LDPath program on one or more Entities (contexts)</p>
     * <p>The site used to perform the search is the one configured through setSite and setLocalSite methods (default is whole Entityhub)</p>
     *
     * @param mixed $contexts The list of entities' URIs used as context for the execution of the LDPath program. It can be a String for a single context or an array of Strings for multiple contexts
     * @param mixed $ldPathProgram The LDPath program to execute. It can be a string containing the LDPath program or an instance of \Stanbol\Services\Entityhub\Model\LDPathProgram
     * @return An RDF Graph with the passed context(s) as subject and the fields selected by the LDPath program as properties
     * and the selected values as object.
     * @throws InvalidArgumentException
     * @throws StanbolServiceException
     */
    public function ldpath($contexts, $ldPathProgram);
}

?>
