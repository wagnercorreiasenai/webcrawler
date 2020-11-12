<?php

class GutenbergCrawler {

    private $url;
    private $proxy;
    private $dom;
    private $html;

    public function __construct() {
        //Seta os valores das variáveis
        $this->url = "http://gutenberg.org/";
        $this->proxy = "10.1.21.254:3128";
        $this->dom = new DOMDocument();
    }

    public function getParagrafos() {
        $this->carregarHtml();
        $tagsDiv = $this->capturarTagsDivGeral();
        $divsInternas = $this->capturarDivsInternasPageContent($tagsDiv);
        $tagsP = $this->capturarTagsP($divsInternas);
        $arrayparagrafos = $this->getArrayParagrafos($tagsP);
        return $arrayparagrafos;
    }
    
    private function getContextoConexao() {
        $arrayConfig = array(
            'http' => array(
                'proxy' => $this->proxy,
                'request_fulluri' => true
            ),
            'https' => array(
                'proxy' => $this->proxy,
                'request_fulluri' => true
            )
        );

        $context = stream_context_create($arrayConfig);
        return $context;
    }

    private function carregarHtml() {
        $context = $this->getContextoConexao();
        $this->html = file_get_contents($this->url, false, $context);

        libxml_use_internal_errors(true);

        //Transforma o html em objeto
        $this->dom->loadHTML($this->html);
        libxml_clear_errors();
    }

    private function capturarTagsDivGeral() {
        $tagsDiv = $this->dom->getElementsByTagName('div');
        return $tagsDiv;
    }

    private function capturarDivsInternasPageContent($divsGeral) {

        $divsInternas = null;

        foreach ($divsGeral as $div) {
            $classe = $div->getAttribute('class');

            if ($classe == 'page_content') {
                $divsInternas = $div->getElementsByTagName('div');
                break;
            }
        }

        return $divsInternas;
    }

    private function capturarTagsP($divsInternas) {

        $tagsP = null;

        foreach ($divsInternas as $divInterna) {
            $classeInterna = $divInterna->getAttribute('class');
            if ($classeInterna == 'box_announce') {
                $tagsP = $divInterna->getElementsByTagName('p');
            }
        }

        return $tagsP;
    }

    private function getArrayParagrafos($tagsP) {
        $arrayParagrafos = [];
        foreach ($tagsP as $p) {
            $arrayParagrafos [] = $p->nodeValue;
        }

        return $arrayParagrafos;
    }

}
