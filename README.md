# Apache Stanbol PHP Client

Apache Stanbol PHP Client is a tool that let [Apache Stanbol](http://stanbol.apache.org/) integrators use Apache Stanbol in an easy way from PHP applications. It covers the REST API for the following Stanbol components:
**Enhancer** and **ContenHub**.

Apache Stanbol PHP Client . The project is organized as a set of REST clients, one for each mentioned Stanbol Component. Each Client component has an implementation for all the RESTful services provided by the component API, managing the requests to the remote services and parsing service's responses for converting them to easy-to-use PHP objects.

Apache Stanbol Client has been developed using third party libraries like [Guzzle](https://guzzle.readthedocs.org/en/latest/) as RESTful client and [EasyRDF](http://www.easyrdf.org/‎) for RDF parsing and representation.

The library makes use of [Composer](https://github.com/composer/composer) to obtain the needed dependencies. Composer is the new PHP package management system that aims to solve the code sharing problem.

## Composer installation
The installation of Composer is really easy:

    curl -s http://getcomposer.org/installer | php -- --install-dir=bin

This will install composer in the bin folder of the current directory. Be sure that the folder you install Composer is on your path if you want to execute Composer from any directory.

## Build library
Current Built Requirements:

* PHP 5+
* Composer

To start working with the project, just clone it in your local workspace:

    git clone https://github.com/adperezmorales/apache-stanbol-php-client.git

In order to download the project dependencies, execute the next command in the library directory:

    composer.phar install

This will download all the needed dependencies and put them in the *vendor* directory.

If you want to execute the tests (written using PHPUnit), run the following command in the library directory:

    phpunit

## How to Use

Below you can find some code examples showing part of the covered features for each Stanbol component. For a full specification of the Apache Stanbol PHP Client API, consider explore the project [PHPDoc]().

### [1. ENHANCER](http://stanbol.apache.org/docs/trunk/components/enhancer/)

#### Simple Content Enhancement

    require("bootstrap.php");

    $configuration = array(); // Advanced Guzzle configuration

    $stanbolClient = new \Stanbol\Client\StanbolClient::getInstance(STANBOL_ENDPOINT, $configuration);
    $enhancements = $stanbolClient->enhancer()->enhance("Paris is the capital of France");

    foreach($enhancements->getTextAnnotations() as $textAnnotation) {
        echo "********************************************\n";
        echo "Selection Context: ".$textAnnotation->getSelectionContext()."\n";  
        echo "Selected Text: ".$textAnnotation->getSelectedText()."\n";
        echo "Engine: ".$textAnnotation->getCreator()."\n";
        echo "Candidates:\n";
        foreach($enhancements->getEntityAnnotations($textAnnotation) as $entityAnnotation) {
              echo "\t" . $entityAnnotation->getEntityLabel() . " - " . $entityAnnotation->getEntityReference()->getUri() . " - " .$entityAnnotation->getConfidence() ."\n";
        }
    }

Produces:

    ********************************************
    Selection Context: Paris is the capital of France
    Selected Text: Paris
    Engine: org.apache.stanbol.enhancer.engines.opennlp.impl.NamedEntityExtractionEnhancementEngine
    Candidates: 
	    Paris - http://dbpedia.org/resource/Paris - 1
	    Paris, Texas - http://dbpedia.org/resource/Paris,_Texas - 0.178778330906

    ********************************************
    Selection Context: Paris is the capital of France
    Selected Text: France
    Engine: org.apache.stanbol.enhancer.engines.opennlp.impl.NamedEntityExtractionEnhancementEngine
    Candidates: 
	    Vichy France - http://dbpedia.org/resource/Vichy_France - 0.195747974547
	    New France - http://dbpedia.org/resource/New_France - 0.234897569456
	    France - http://dbpedia.org/resource/France - 1

#### Get best Entity Annotation for each Text Annotation

    $enhancements = $stanbolClient->enhancer()->enhance("Paris is the capital of France");
    $enhancements->getBestAnnotations()

This piece of code will return an array where each element is composed by another array containing the Text Annotation at the index 0 and the best Entity Annotation (with the higher confidence value) at the index 1.

#### Filter By Confidence

    $enhancements = $stanbolClient->enhancer()->enhance("Paris is the capital of France");
    $enhancements->getTextAnnotationsByConfidenceValue($confidenceValue); // Return the text annotations whose confidence value is greater than ***$confidenceValue***
    $enhancements->getEntityAnnotationsByConfidenceValue($confidenceValue); // Return the entity annotations whose confidence value is greater than ***$confidenceValue***
    $enhancements->getEntitiesByConfidenceValue($confidenceValue); // Return the entities which are referenced by entity annotations whose confidence value is greater than ***$confidenceValue***

### [2. ENTITYHUB](http://stanbol.apache.org/docs/trunk/components/entityhub/)

#### Entity CRUD

    // Create
    $entityhubService = $stanbolClient->entityhub();
    $entityhubService->setSite(SITE); // If you want to add the entity to a custom site instead of memory site
    $entity = new \Stanbol\Vocabulary\Model\Entity(ENTITY_URI);
    $entity->addPropertyValue($property, $value, $lang);
    $entityhubService->create($entity); // Return the entity id if the entity was created successfully or throw an exception if the entity could not be created or a connection error occurred

    // Create from file
    $entityhubService->createFromFile(FILE); // Return the entity id if the entity was created successfully or throw an exception if an error occurred

    // Retrieve
    $entity = $entityhubService->get(RESOURCE_ID); // Return the entity if exists or throw an exception if an error ocurred

    // Delete
    $deleted = $entityhubService->delete(RESOURCE_ID); // Return a boolean indicating whether the entity has been successfully deleted or not

#### Entity Search

    $ldPathProgram = 
        "@prefix find:<http://stanbol.apache.org/ontology/entityhub/find/>; 
         find:labels = rdfs:label[@en] :: xsd:string; 
         find:comment = rdfs:comment[@en] :: xsd:string; 
         find:categories = dc:subject :: xsd:anyURI; 
         find:mainType = rdf:type :: xsd:anyURI;";

    // Search in DBPedia Referenced Site
    $entityhubService->setSite('dbpedia');
    $program = new LDPathProgram(ldPathProgram);
    $entities = $entityhubService->find("Par*", null, "en", $program, 10, 0); // Return the list of entities

#### Get Entity Properties using LDPath programs

    $ldPathProgram = 
        "@prefix find:<http://stanbol.apache.org/ontology/entityhub/find/>; 
         find:labels = rdfs:label[@en] :: xsd:string; 
         find:comment = rdfs:comment[@en] :: xsd:string; 
         find:categories = dc:subject :: xsd:anyURI; 
         find:mainType = rdf:type :: xsd:anyURI;";

    // Use DBPedia Referenced Site
    $entityhubService->setSite('dbpedia');
    $program = new \Stanbol\Services\Entityhub\Model\LDPathProgram($ldPathProgram);
    $properties = $entityhubService->ldpath("http://dbpedia.org/resource/Paris", $ldpathProgram); // Return the associative array with properties and values. The resulting properties are mapped using the supplied LDPath program

#### Get configured sites

    $sites = $entityhubService->getReferencedSites(); // Return the list of configured Sites

    