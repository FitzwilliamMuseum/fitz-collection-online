<?php

namespace App;

use EasyRdf\Format;
use EasyRdf\Graph;
class RdfConvertor

{

    public function __construct(public array $input_format_options = array('Guess' => 'guess'), public array $output_format_options = array('Format' => 'rdfxml'))
    {

    }
    public function getFormats(): array
    {
        $formats = Format::getFormats();
        foreach($formats as $format){
            if ($format->getSerialiserClass()) {
                $this->output_format_options[$format->getLabel()] = $format->getName();
            }
            if ($format->getParserClass()) {
                $this->input_format_options[$format->getLabel()] = $format->getName();
            }
        }
        return $this->input_format_options;
    }

    public function convert($uri)
    {
        dump($this->getFormats());
        $graph = Graph::newAndLoad('https://data.getty.edu/museum/collection/object/9e46001b-2cbe-45ee-aa6b-6c2d03cb9967');
        dd($graph);
    }
}
