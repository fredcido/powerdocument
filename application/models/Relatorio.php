<?php

/**
 *
 */
class Model_Relatorio extends App_Model_Abstract
{
    protected $_mapper;
    
    public function __construct()
    {
	$this->_mapper = new Model_Mapper_Relatorio();
    }
    
    /**
     *
     * @return array
     */
    public function graficoAcoes()
    {
	$dadosGrafico = $this->_mapper->contabilizaAcoesHora();
	
	$dadosRetorno = array(
	  'horarios'	  => array(),
	  'downloads'	  => array(),
	  'uploads'	  => array()
	);
	
	foreach ( $dadosGrafico as $grafico ) {
	    
	    $dadosRetorno['horarios'][] = $grafico->horario;
	    $dadosRetorno['downloads'][] = (int)$grafico->downloads;
	    $dadosRetorno['uploads'][] = (int)$grafico->uploads;
	}
	
	return $dadosRetorno;
    }
    
    /**
     *
     * @return array
     */
    public function graficoExtensoes()
    {
	$dadosGrafico = $this->_mapper->contabilizaExtensoes();
	
	$downloads = array();
	$uploads = array();
	
	foreach ( $dadosGrafico as $grafico ) {
	    
	    $downloads[] = array(
		'name' => $grafico->descricao,
		'y'    => (int)$grafico->downloads
	    );
	    
	    $uploads[] = array(
		'name' => $grafico->descricao,
		'y'    => (int)$grafico->uploads
	    );
	}
	
	return compact( 'downloads', 'uploads' );
    }
    
    /**
     *
     * @return array
     */
    public function graficoCategorias()
    {
	$dadosGrafico = $this->_mapper->contabilizaCategoras();
	
	$downloads = array();
	$uploads = array();
	
	foreach ( $dadosGrafico as $grafico ) {
	    
	    $downloads[] = array(
		'name' => $grafico->nome,
		'y'    => (int)$grafico->downloads
	    );
	    
	    $uploads[] = array(
		'name' => $grafico->nome,
		'y'    => (int)$grafico->uploads
	    );
	}
	
	return compact( 'downloads', 'uploads' );
    }
    
    /**
     *
     * @return array
     */
    public function graficoPerfil()
    {
	$dadosGrafico = $this->_mapper->contabilizaPerfil();
		
	$dadosRetorno = array(
	  'perfis'	  => array(),
	  'downloads'	  => array(),
	  'uploads'	  => array()
	);
	
	foreach ( $dadosGrafico as $grafico ) {
	    
	    $dadosRetorno['perfis'][] = $grafico->nome;
	    $dadosRetorno['downloads'][] = (int)$grafico->downloads;
	    $dadosRetorno['uploads'][] = (int)$grafico->uploads;
	}
	
	return $dadosRetorno;
    }
    
    /**
     *
     * @return array
     */
    public function graficoBaixados()
    {
	$dadosGrafico = $this->_mapper->contabilizaBaixados();
		
	$dadosRetorno = array(
	  'arquivos'	  => array(),
	  'downloads'	  => array()
	);
	
	foreach ( $dadosGrafico as $grafico ) {
	    
	    $dadosRetorno['arquivos'][] = $grafico->nome;
	    $dadosRetorno['downloads'][] = (int)$grafico->total;
	}
	
	return $dadosRetorno;
    }
}