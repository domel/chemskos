<?php

EasyRdf_Namespace::set('c', 'http://chemskos.com/ns#');
EasyRdf_Namespace::set('dbo', 'http://dbpedia.org/ontology/');
EasyRdf_Namespace::set('dbp', 'http://dbpedia.org/property/');

/**
 * Class for handling concept property values.
 */
class ConceptPropertyValue extends VocabularyDataObject
{
    /** submembers */
    private $submembers;
    /** property type */
    private $type;
    /** content language */
    private $clang;


    public function __construct($model, $vocab, $resource, $prop, $clang = '')
    {
        parent::__construct($model, $vocab, $resource);
        $this->submembers = array();
        $this->type = $prop;
        $this->clang = $clang;
    }

    public function __toString()
    {
        $label = is_string($this->getLabel()) ? $this->getLabel() : $this->getLabel()->getValue();
        if ($this->vocab->getConfig()->sortByNotation()) {
            //$label = $this->getNotation() . $label;
            $label = $label;
        }

        return $label;
    }

    public function getLang()
    {
        return $this->getEnvLang();
    }

    public function getLabel($lang = '')
    {
        if ($this->clang) {
            $lang = $this->clang;
        }

        if ($this->resource->label($lang) !== null) { // current language
            return $this->resource->label($lang);
        } elseif ($this->resource->label($this->vocab->getConfig()->getDefaultLanguage()) !== null) { // vocab default language
            return $this->resource->label($this->vocab->getConfig()->getDefaultLanguage());
        } elseif ($this->resource->label() !== null) { // any language
            return $this->resource->label();
        } elseif ($this->resource->getLiteral('rdf:value', $lang) !== null) { // current language
            return $this->resource->getLiteral('rdf:value', $lang);
        } elseif ($this->resource->getLiteral('rdf:value') !== null) { // any language
            return $this->resource->getLiteral('rdf:value');
        }
        $label = $this->resource->shorten() ? $this->resource->shorten() : $this->getUri();
        return $label;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getUri()
    {
        return $this->resource->getUri();
    }

    public function getVocab()
    {
        return $this->vocab;
    }

    public function getVocabName()
    {
        return $this->vocab->getTitle();
    }

    public function addSubMember($member, $lang = '')
    {
        $label = $member->getLabel($lang) ? $member->getLabel($lang) : $member->getLabel();
        $this->submembers[$label->getValue()] = $member;
        $this->sortSubMembers();
    }

    public function getSubMembers()
    {
        if (empty($this->submembers)) {
            return null;
        }

        return $this->submembers;
    }

    private function sortSubMembers()
    {
        if (!empty($this->submembers)) {
            ksort($this->submembers);
        }

    }

    public function isExternal() {
        $propertyUris = $this->resource->propertyUris();
        return empty($propertyUris);
    }

    public function getNotation()
    {
        if ($this->vocab->getConfig()->showNotation() && $this->resource->get('skos:notation')) {
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


    public function getExample()
    {
        if ($this->resource->get('skos:example')) {
            return $this->resource->get('skos:example')->getValue();
        }
    }

    public function getPartUrl()
    {
        $zm = str_replace('page/', '', $_SERVER['PHP_SELF']);
        $zm = str_replace('/index.php', '', $zm);
        return $zm;
    }

    public function isReified() {
        return (!$this->resource->label() && $this->resource->getLiteral('rdf:value'));
    }
    
    public function getReifiedPropertyValues() {
        $ret = array();
        $props = $this->resource->propertyUris();
        foreach($props as $prop) {
            $prop = (EasyRdf_Namespace::shorten($prop) !== null) ? EasyRdf_Namespace::shorten($prop) : $prop;
            foreach ($this->resource->allLiterals($prop) as $val) {
                if ($prop !== 'rdf:value' && $this->resource->get($prop)) { // shown elsewhere
                    $ret[gettext($prop)] = new ConceptPropertyValueLiteral($this->resource->get($prop), $prop);
                }
            }
            foreach ($this->resource->allResources($prop) as $val) {
                $ret[gettext($prop)] = new ConceptPropertyValue($this->model, $this->vocab, $val, $prop, $this->clang);
            }
        }
        return $ret;
    }

}
