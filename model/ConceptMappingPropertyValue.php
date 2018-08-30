<?php

EasyRdf_Namespace::set('c', 'http://chemskos.com/ns#');
EasyRdf_Namespace::set('dbo', 'http://dbpedia.org/ontology/');
EasyRdf_Namespace::set('dbp', 'http://dbpedia.org/property/');

/**
 * Class for handling concept property values.
 */
class ConceptMappingPropertyValue extends VocabularyDataObject
{
    /** property type */
    private $type;
    private $clang;
    private $labelcache;

    public function __construct($model, $vocab, $resource, $prop, $clang = '')
    {
        parent::__construct($model, $vocab, $resource);
        $this->type = $prop;
        $this->clang = $clang;
        $this->labelcache = array();
    }

    public function __toString()
    {
        $label = $this->getLabel();
        //$notation = $this->getNotation();
        //return $notation . $label;
        return $label;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getLabel($lang = '')
    {
        if (isset($this->labelcache[$lang])) {
            return $this->labelcache[$lang];
        }

        $label = $this->queryLabel($lang);
        $this->labelcache[$lang] = $label;
        return $label;
    }

    private function queryLabel($lang)
    {
        if ($this->clang) {
            $lang = $this->clang;
        }

        $exvocab = $this->model->guessVocabularyFromURI($this->resource->getUri());

        if ($this->resource->label($lang) !== null) { // current language
            return $this->resource->label($lang);
        } elseif ($this->resource->label() !== null) { // any language
            return $this->resource->label();
        } elseif ($this->resource->getLiteral('rdf:value', $lang) !== null) { // current language
            return $this->resource->getLiteral('rdf:value', $lang);
        } elseif ($this->resource->getLiteral('rdf:value') !== null) { // any language
            return $this->resource->getLiteral('rdf:value');
        }

        // if the resource is from a another vocabulary known by the skosmos instance
        if ($exvocab) {
            $label = $this->getExternalLabel($exvocab, $this->getUri(), $lang) ? $this->getExternalLabel($exvocab, $this->getUri(), $lang) : $this->getExternalLabel($exvocab, $this->getUri(), $exvocab->getConfig()->getDefaultLanguage());
            if ($label) {
                return $label;
            }
        }

        // using URI as label if nothing else has been found.
        $label = $this->resource->shorten() ? $this->resource->shorten() : $this->resource->getUri();
        return $label;
    }

    public function getUri()
    {
        return $this->resource->getUri();
    }

    public function getExVocab()
    {
        $exvocab = $this->model->guessVocabularyFromURI($this->getUri());
        return $exvocab;
    }

    public function getVocab()
    {
        return $this->vocab;
    }

    public function getVocabName()
    {
        $exvocab = $this->model->guessVocabularyFromURI($this->resource->getUri());
        if ($exvocab) {
            return $exvocab->getTitle();
        }
        // @codeCoverageIgnoreStart
        $scheme = $this->resource->get('skos:inScheme');
        if ($scheme) {
            $schemeResource = $this->model->getResourceFromUri($scheme->getUri());
            if ($schemeResource && $schemeResource->label()) {
                return $schemeResource->label()->getValue();
            }
        }
        // got a label for the concept, but not the scheme - use the host name as scheme label
        return parse_url($this->resource->getUri(), PHP_URL_HOST);
        // @codeCoverageIgnoreEnd
    }

    public function getNotation()
    {
        if ($this->resource->get('skos:notation')) {
            return $this->resource->get('skos:notation')->getValue();
        }
    }

    public function getDefinition()
    {
        if ($this->resource->get('skos:definition')) {
            return $this->resource->get('skos:definition')->getValue();
        }
    }

    public function getSeeAlso()
    {
        if ($this->resource->resourcesMatching('rdfs:seeAlso')) {
            return $this->resource->resourcesMatching('rdfs:seeAlso')->getUri();
        }
    }

    public function getMass()
    {
        if ($this->resource->get('dbp:molecularWeight')) {
            return $this->resource->get('dbp:molecularWeight')->getValue();
        }
    }

    public function getInchi()
    {
        if ($this->resource->get('dbp:inchi')) {
            return $this->resource->get('dbp:inchi')->getValue();
        }
    }

    public function getInchiKey()
    {
        if ($this->resource->get('dbp:inchikey')) {
            return $this->resource->get('dbp:inchikey')->getValue();
        }
    }

    public function getCasNumber()
    {
        if ($this->resource->get('dbp:casNumber')) {
            return $this->resource->get('dbp:casNumber')->getValue();
        }
    }

    public function getExample()
    {
        if ($this->resource->get('skos:example')) {
            return $this->resource->get('skos:example')->getValue();
        }
    }

    public function getSearchSds()
    {
        if ($this->resource->resourcesMatching('c:searchSds')) {
            return $this->resource->resourcesMatching('c:searchSds')->getUri();
        }
    }

    public function getSearchSp()
    {
        if ($this->resource->resourcesMatching('c:searchSp')) {
            return $this->resource->resourcesMatching('c:searchSp')->getUri();
        }
    }

    public function getSearchPubMed()
    {
        if ($this->resource->resourcesMatching('c:searchPubMed')) {
            return $this->resource->resourcesMatching('c:searchPubMed')->getUri();
        }
    }

    public function getSearchGoogleScholar()
    {
        if ($this->resource->resourcesMatching('c:searchGoogleScholar')) {
            return $this->resource->resourcesMatching('c:searchGoogleScholar')->getUri();
        }
    }

    public function getSearchGoogleBooks()
    {
        if ($this->resource->resourcesMatching('c:searchGoogleBooks')) {
            return $this->resource->resourcesMatching('c:searchGoogleBooks')->getUri();
        }
    }

    public function getSearchChemSpider()
    {
        if ($this->resource->resourcesMatching('c:searchChemSpider')) {
            return $this->resource->resourcesMatching('c:searchChemSpider')->getUri();
        }
    }

    public function getPartUrl()
    {
        $zm = str_replace('page/', '', $_SERVER['PHP_SELF']);
        $zm = str_replace('/index.php', '', $zm);
        return $zm;
    }



}
