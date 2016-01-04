<?php

class Zend_Controller_Action_Helper_Geral extends Zend_Controller_Action_Helper_Abstract
{
    public function encodeArrayUTF8( $arrayMaster, $encode = true )
    {
        //Verifica se elemento passado � um array
        if ( is_array( $arrayMaster ) ) {
        //Percorre cada posi��o do array chamando recursivamente o mesmo m�todo
            foreach ( $arrayMaster as $id=>$elemento ) {
                $arrayMaster[$id] = $this->encodeArrayUTF8( $elemento, $encode );
            }
        } else {
        //Verifica se valor � string para Codificar
            if ( is_string( $arrayMaster ) )
	            //Se elemento n�o for um array, codifica o valor pra UTF-8
                $arrayMaster = ($encode) ? utf8_encode($arrayMaster) : utf8_decode($arrayMaster);
        }
        //Retorna array ou valor convertido
        return $arrayMaster;
    }

}