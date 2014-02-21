<?php

namespace Stanbol\Services\Enhancer\Model;

use Stanbol\Services\Enhancer\Model\Enhancement;

/**
 * <p>TextAnnotation Model</p>
 * <p>Represents a TextAnnotation in the Enhancement model</p>
 *
 * @author Antonio David Pérez Morales <adperezmorales@gmail.com>
 */
class TextAnnotation extends Enhancement
{

    // properties
    private $type = null; // http://purl.org/dc/terms/type
    private $starts = 0; // http://fise.iks-project.eu/ontology/start
    private $ends = 0; // http://fise.iks-project.eu/ontology/end
    private $selectedText = null; // http://fise.iks-project.eu/ontology/selected-text
    private $selectionContext = null; // http://fise.iks-project.eu/ontology/selection-context

    public function __construct($uri = '')
    {
        parent::__construct($uri);
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getStarts()
    {
        return $this->starts;
    }

    public function setStarts($starts)
    {
        $this->starts = $starts;
    }

    public function getEnds()
    {
        return $this->ends;
    }

    public function setEnds($ends)
    {
        $this->ends = $ends;
    }

    public function getSelectedText()
    {
        return $this->selectedText;
    }

    public function setSelectedText($selectedText)
    {
        $this->selectedText = $selectedText;
    }

    public function getSelectionContext()
    {
        return $this->selectionContext;
    }

    public function setSelectionContext($selectionContext)
    {
        $this->selectionContext = $selectionContext;
    }

}
?>
