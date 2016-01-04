<?php

class App_Form_Padrao extends Zend_Form
{
    /**
     *
     * @var <array>
     */
    protected $_elementsForm = array();

    /**
     *
     * @var <array>
     */
    protected $_buttons = array();

    
    /**
     *
     * @return <array>
     */
    public function getDefaultElementDecorators()
    {
            return array(
                            array( 'ViewHelper', array( 'class' => 'text', 'placement' => 'prepend' )),
                            array( array( 'wrapper' => 'HtmlTag' ), array( 'tag' => 'span', 'class' => 'input_wrapper' ) ),
                            'Description',
                            array( array( 'input' => 'HtmlTag' ), array( 'tag' => 'div', 'class' => 'inputs' ) ),
                            array( 'Label', array( 'requiredSuffix' => ':*', 'optionalSuffix' => ':' ) ),
			    'Errors',
                            array( array( 'row' => 'HtmlTag' ), array( 'tag' => 'div', 'class' => 'row' ) )
                       );
    }

     /**
     *
     * @return <array>
     */
    public function getDefaultMultiElementDecorators( $class = 'inline')
    {
            return array(
                            array( 'ViewHelper', array( 'class' => 'text', 'placement' => 'prepend' )),
			    array( array( 'list' => 'HtmlTag' ), array( 'tag' => 'li' ) ),
                            array( array( 'wrapper' => 'HtmlTag' ), array( 'tag' => 'ul', 'class' => $class ) ),
                            'Description',
                            array( array( 'input' => 'HtmlTag' ), array( 'tag' => 'div', 'class' => 'inputs' ) ),
                            array( 'Label', array( 'requiredSuffix' => ':*', 'optionalSuffix' => ':' ) ),
			    'Errors',
                            array( array( 'row' => 'HtmlTag' ), array( 'tag' => 'div', 'class' => 'row' ) )
                       );
    }


    /**
     *
     * @return <array>
     */
    public function getDefaultTextAreaDecorators()
    {
            return array(
                            array( 'ViewHelper', array( 'class' => 'text', 'placement' => 'prepend' )),
                            array( array( 'wrapper' => 'HtmlTag' ), array( 'tag' => 'span', 'class' => 'input_wrapper textarea_wrapper' ) ),
                            'Description',
                            array( array( 'input' => 'HtmlTag' ), array( 'tag' => 'div', 'class' => 'inputs' ) ),
                            array( 'Label', array( 'requiredSuffix' => ':*', 'optionalSuffix' => ':' ) ),
			    'Errors',
                            array( array( 'row' => 'HtmlTag' ), array( 'tag' => 'div', 'class' => 'row' ) )
                       );
    }
    
    /**
     *
     * @return <array>
     */
    public function getDefaultFileDecorators()
    {
            return array(
                            array( array( 'fake' => 'HtmlTag' ), array( 'tag' => 'div', 'class' => 'fakeupload' ) ),
                            array( 'File', array( 'class' => 'realupload') ),
                            array( array( 'container' => 'HtmlTag' ), array( 'tag' => 'div', 'class' => 'div_file' ) ),
                            array( array( 'wrapper' => 'HtmlTag' ), array( 'tag' => 'span', 'class' => 'input_wrapper input_file_wrapper' ) ),
                            'Description',
                            array( array( 'input' => 'HtmlTag' ), array( 'tag' => 'div', 'class' => 'inputs' ) ),
                            array( 'Label', array( 'requiredSuffix' => ':*', 'optionalSuffix' => ':' ) ),
			    'Errors',
                            array( array( 'row' => 'HtmlTag' ), array( 'tag' => 'div', 'class' => 'row' ) )
                       );
    }



    /**
     *
     * @return <array>
     */
    public function getDefaultFormDecorators()
    {
            return array(
                          //'FormErrors',
                          'FormElements',
                          array( array( 'div' => 'HtmlTag' ), array( 'tag' => 'div', 'class' => 'forms' ) ),
                          array( array( 'fieldset' => 'HtmlTag' ), array( 'tag' => 'fieldset' ) ),
                          array( 'Form', array('class' => 'search_form geral_form' ) )
                    );
    }

    /**
     *
     * @return <array>
     */
    public function getDefaultButtonDecorators()
    {
        return array(
                          array( 'Description', array( 'tag' => 'span' ) ),
                          array( array( 'labelWrapper' => 'HtmlTag' ), array( 'tag' => 'span' ) ),
                          'ViewHelper',
                          'Errors',
                          array( array( 'buttonWrapper' => 'HtmlTag' ), array( 'tag' => 'span', 'class' => 'button gray_button' ) )
                    );
    }

    /**
     *
     * @return <bool>
     */
    protected function setToolbar()
    {
        $buttonsName = array();
        foreach ( $this->_buttons as $buttonElement )
            $buttonsName[] = $buttonElement->getName();

        if ( empty( $buttonsName ) )
            return false;

        $this->addELements( $this->_buttons );

        $this->addDisplayGroup( $buttonsName, 'toolbar');

        $toolbar = $this->getDisplayGroup('toolbar');
        $toolbar->setDecorators(
                    array(
                        'FormElements',
                        array( 'HtmlTag', array( 'tag' => 'div', 'class' => 'row toolbar' ) )
                    )
                );

        return true;
    }
}
