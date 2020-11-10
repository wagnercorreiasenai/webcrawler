<?php

//Configurações de proxy SENAI
$proxy = '10.1.21.254:3128';

$arrayConfig = array(
    'http' => array(
        'proxy' => $proxy,
        'request_fulluri' => true
    ),
    'https' => array(
        'proxy' => $proxy,
        'request_fulluri' => true
    )
);

$context = stream_context_create($arrayConfig);
//----------------------------------------------

$url = "http://gutenberg.org/";
$html = file_get_contents($url, false, $context);

$dom = new DOMDocument();
libxml_use_internal_errors(true);

//Transforma o html em objeto
$dom->loadHTML($html);
libxml_clear_errors();

//Caputura as tags p
$tagsDiv = $dom->getElementsByTagName('div');

foreach ($tagsDiv as $div) {
    $classe = $div->getAttribute('class');
    
    if ($classe == 'page_content') {
        $divsInternas = $div->getElementsByTagName('div');
        
        foreach ($divsInternas as $divInterna) {
            $classeInterna = $divInterna->getAttribute('class');
            if ($classeInterna == 'box_announce') {
                echo $divInterna->nodeValue;
                break;
            }
        }
        
        break;
    }
    
    
}