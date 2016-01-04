<?php

/** 
 * 
 */
class App_View_Helper_Calendar extends Zend_View_Helper_Abstract
{
	/**
	 * 
	 * @var DOMDocument
	 */
	protected $_dom;
	
	/**
	 * 
	 * @var Zend_Date
	 */
	protected $_date;
	
	/**
	 * 
	 * @var array
	 */
	protected $_events = array();
	
	/**
	 * 
	 * @var int
	 */
	protected $_countEvent = 0;
	
	/**
	 * 
	 * @var array
	 */
	protected $_dia = array(
		'Domingo',
		'Segunda-feira',
		'Terça-feira',
		'Quarta-feira',
		'Quinta-feira',
		'Sexta-feira',
		'Sábado'
	);

	/**
	 * 
	 * @access 	public
	 * @param 	string 	$date
	 * @param 	array 	$events
	 * @return 	void
	 */
	public function calendar ( $date = null, array $events = null )
	{
		$this->_dom 	= new DOMDocument();
		$this->_date 	= new Zend_Date( $date );
		
		$this->_setEvent( $events );
		$this->_initTable();
		
		return $this;
	}
	
	/**
	 * 
	 * @access 	public
	 * @param 	array|null $events
	 * @return 	void
	 */
	protected function _setEvent( $events )
	{
		if ( is_array($events) )
			$this->_events = $events;
	}
	
	/**
	 * 
	 * @access protected
	 * @return void
	 */
	protected function _initTable ()
	{
		$table = $this->_dom->createElement( 'table' );
		$table = $this->_dom->appendChild( $table );
		
		$table->setAttribute( 'cellspacing', '0' );
		$table->setAttribute( 'class', 'calendar' );
		
		$this->_initThead($table);
		$this->_initBody($table);
	}
	
	/**
	 * 
	 * @access 	protected
	 * @param 	DOMElement $table
	 * @return 	void
	 */
	protected function _initThead ( DOMElement $table )
	{
		$thead = $this->_dom->createElement( 'thead' );
		$thead = $table->appendChild( $thead );
		
		$tr = $this->_dom->createElement( 'tr' );
		$tr = $thead->appendChild( $tr );
		
		for ( $i = 0; $i < 8; $i++ ) {
			
			$th = $this->_dom->createElement( 'th' );
			$th = $tr->appendChild( $th );
			
			switch ( $i ) {
				
				case 0:
					$th->setAttribute( 'class', 'black-cell' );
					$th->setAttribute( 'scope', 'col' );
					
					$span = $this->_dom->createElement( 'span' );
					$span = $th->appendChild( $span );
					break;
					
				case 1:
				case 7:
					$th->setAttribute( 'class', 'week-end' );
					$th->setAttribute( 'scope', 'col' );
					
					$text = $this->_dom->createTextNode( $this->_dia[$i - 1] );
					$text = $th->appendChild( $text );
					break;
					
				default:
					$th->setAttribute( 'scope', 'col' );
					
					$text = $this->_dom->createTextNode( $this->_dia[$i - 1] );
					$text = $th->appendChild( $text );
				
			}
			
		}
	}
	
	/**
	 * 
	 * @access protected
	 * @return int
	 */
	protected function _firstWeekday ()
	{
		$day = (int) $this->_date->toString('dd');
		
		//Seleciona primeiro dia do mes
		$this->_date->set( 1, Zend_Date::DAY );
		
		//Dia da semana
		$weekday = $this->_date->get( Zend_Date::WEEKDAY_DIGIT );
		
		$this->_date->set( $day, Zend_Date::DAY );
		
		return $weekday;
	}
	
	/**
	 * 
	 * @access 	protected
	 * @param 	DOMElement $table
	 * @return 	void
	 */
	protected function _initBody ( DOMElement $table )
	{
		//Quantidade de dias no mes
		$days = $this->_date->get( Zend_Date::MONTH_DAYS );
		
		//Primeiro dia da semana do mes
		$weekday = $this->_firstWeekday();
		
		$countWeek = 1; //Conta total de semanas
		$countLastDay = 0; //Conta total de dias do mes anterior
		
		$tbody = $this->_dom->createElement( 'tbody' );
		$tbody = $table->appendChild( $tbody );
		
		$tr = $this->_dom->createElement( 'tr' );
		$tr = $tbody->appendChild( $tr );
		
		$th = $this->_dom->createElement( 'th' );
		$th = $tr->appendChild( $th );
		
		for ( $i = 0; $i < $days + $weekday; $i++ ) {
			
			//Verifica se vai adicionar uma nova linha na tabela
			if ( (0 == $i % 7) && !empty($i) ) {

				$countWeek++;
				
				$tr = $this->_dom->createElement( 'tr' );
				$tr = $tbody->appendChild( $tr );
				
				$th = $this->_dom->createElement( 'th' );
				$th = $tr->appendChild( $th );
				
			}
		
			//Dia da semana que o mes inicia
			if ( $weekday <= $i ) {
				
				$day = $i + 1 - $weekday;
				
				$this->_addActiveDay( $tr, $day );
				
			} else {
				
				$countLastDay++;
			
				$this->_addInactiveDay($tr);
				
			}
			
		}
		
		$this->_addInactiveDay( $tr, ($countWeek * 7 - $countLastDay - $days) );
	}
	
	/**
	 * 
	 * @access 	protected
	 * @param 	DOMElement $tr
	 * @param 	int $day
	 * @return 	void
	 */
	protected function _addActiveDay ( DOMElement $tr, $day )
	{
		$date = clone $this->_date;
		
		$date->set( $day, Zend_Date::DAY );
		
		$td = $this->_dom->createElement( 'td' );
		$td = $tr->appendChild( $td );
		
		if ( in_array( $date->get(Zend_Date::WEEKDAY_DIGIT), array(0, 6) ) && !$date->equals( Zend_Date::now(), Zend_Date::DATES ) )
			$td->setAttribute( 'class', 'week-end' );
		
		$a = $this->_dom->createElement( 'a' );
		$a = $td->appendChild( $a );
		
		if ( $date->equals( Zend_Date::now(), Zend_Date::DATES ) )
			$a->setAttribute( 'class', 'day today' );
		else
			$a->setAttribute( 'class', 'day' );
		
		$a->setAttribute( 'href', 'javascript:;' );
		
		$textDay = $this->_dom->createTextNode( $day );
		$textDay = $a->appendChild( $textDay );
			
		if ( $date->isLater( Zend_Date::now() ) ) {
		
			$a = $this->_dom->createElement( 'a' );
			$a = $td->appendChild( $a );
			
			$a->setAttribute( 'class', 'add-event' );
			$a->setAttribute( 'href', "javascript:addEvent('".$date->toString('dd/MM/yyyy')."');" );
			
			$textDay = $this->_dom->createTextNode( 'Add' );
			$textDay = $a->appendChild( $textDay );
			
		}
		
		$this->_addEvent( $td, $day );
	}
	
	/**
	 * 
	 * @access 	protected
	 * @param 	DOMElement $tr
	 * @param 	int $count
	 */
	protected function _addInactiveDay ( DOMElement $tr, $count = 1 )
	{
		while ( 0 < $count ) {
		
			$td = $this->_dom->createElement( 'td' );
			$td = $tr->appendChild( $td );
		
			$td->setAttribute( 'class', 'unavailable' );
		
			$count--;
			
		}
	}

	/**
	 * 
	 * @access 	protected
	 * @param 	DOMElement $td
	 * @param 	int $day
	 * @return 	void
	 */
	protected function _addEvent ( DOMElement $td, $day )
	{
		if ( in_array( $day, array_keys($this->_events) ) ) {
		
			$ul = $this->_dom->createElement( 'ul' );
			$ul = $td->appendChild( $ul );
		
			$ul->setAttribute( 'class', 'events with-children-tip' );
			
			$events = $this->_events[$day];
			
			foreach ( $events as $event ) {
				
				$this->_countEvent++;
				
				$li = $this->_dom->createElement( 'li' );
				$li = $ul->appendChild( $li );
				
				$li->setAttribute( 'title', $event['description'] );
				
				if ( empty($event['status']) )
					$li->setAttribute( 'class', 'red' );
				
				$a = $this->_dom->createElement( 'a' );
				$a = $li->appendChild( $a );
				
				$a->setAttribute( 'href', 'javascript:edit('.$event['id'].');' );
				
				$b = $this->_dom->createElement( 'b' );
				$b = $a->appendChild( $b );
				
				$textHours = $this->_dom->createTextNode( $event['hours'] );
				$textHours = $b->appendChild( $textHours );
				
				$a->appendChild( $b );
				
				$textEvent = $this->_dom->createTextNode( $event['description'] );
				$textEvent = $a->appendChild( $textEvent );
				
			}
		
		}
	}
	
	/**
	 * 
	 * @access public
	 * @return string
	 */
	public function getMonthYear ()
	{
		return $this->_date->toString('MMMM yyyy');
	}
	
	/**
	 * 
	 * @access 	public
	 * @param 	string $format
	 * @return 	string
	 */
	public function previous ( $format = 'yyyy-MM-dd' )
	{
		$date = clone $this->_date;
		
		$date->set( 1, Zend_Date::DAY );
		$date->sub( 1, Zend_Date::MONTH );
		
		return $date->toString( $format );
	}
	
	/**
	 * 
	 * @access 	public
	 * @param 	string $format
	 * @return 	string
	 */
	public function next ( $format = 'yyyy-MM-dd' )
	{
		$date = clone $this->_date;
		
		$date->set( 1, Zend_Date::DAY );
		$date->add( 1, Zend_Date::MONTH );
		
		return $date->toString( $format );
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function getControls ()
	{
		$dom = new DOMDocument();
		
		$ul = $dom->createElement( 'ul' );
		$ul = $dom->appendChild( $ul );
		
		$ul->setAttribute( 'class', 'controls-buttons' );
		
		$li = $dom->createElement( 'li' );
		$li = $ul->appendChild( $li );
		
		$a = $dom->createElement( 'a' );
		$a = $li->appendChild( $a );
		
		$a->setAttribute( 'href', "javascript:loadCalendar('" . $this->previous() . "');" );
		
		$img = $dom->createElement( 'img' );
		$img = $a->appendChild( $img );
		
		$img->setAttribute( 'width', '16' );
		$img->setAttribute( 'height', '16' );
		$img->setAttribute( 'src', $this->view->baseUrl('public/admin/images/icons/fugue/navigation-180.png') );
		
		$li = $dom->createElement( 'li' );
		$li = $ul->appendChild( $li );
		
		$li->setAttribute( 'class', 'sep' );
		
		$li = $dom->createElement( 'li' );
		$li = $ul->appendChild( $li );
		
		$li->setAttribute( 'class', 'controls-block' );
		
		$strong = $dom->createElement( 'strong' );
		$strong = $li->appendChild( $strong );
		
		$text = $dom->createTextNode( $this->getMonthYear() );
		$text = $strong->appendChild( $text );
		
		$li = $dom->createElement( 'li' );
		$li = $ul->appendChild( $li );
		
		$li->setAttribute( 'class', 'sep' );
		
		$li = $dom->createElement( 'li' );
		$li = $ul->appendChild( $li );
		
		$a = $dom->createElement( 'a' );
		$a = $li->appendChild( $a );
		
		$a->setAttribute( 'href', "javascript:loadCalendar('" . $this->next() . "');" );
		
		$img = $dom->createElement( 'img' );
		$img = $a->appendChild( $img );
		
		$img->setAttribute( 'width', '16' );
		$img->setAttribute( 'height', '16' );
		$img->setAttribute( 'src', $this->view->baseUrl('public/admin/images/icons/fugue/navigation.png') );
		
		return $dom->saveHTML();
	}
	
	/**
	 * 
	 * @access public
	 * @return string
	 */
	public function getCalendar ()
	{
		return $this->_dom->saveHTML();
	}
	
	/**
	 * 
	 * @access public
	 * @return int
	 */
	public function getCountEvent ()
	{
		switch ( $this->_countEvent ) {
			case 0:
				return 'Nenhum evento encontrado.';
				break;

			case 1:
				$message = ' Evento encontrado.';
				break;
				
			default:
				$message = ' Eventos encontrados.';
		}
		 
		return $this->_countEvent . $message;
	}
}