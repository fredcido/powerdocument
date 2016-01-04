// Configuracao de exportacao para todos os graficos
var exportingChart = {
    buttons: {
	exportButton: {
	    menuItems: [
	    {
		text: 'Exportar pra PNG',
		onclick: function()
		{
		    this.exportChart({
			type: 'image/png'
		    });
		}
	    },
	    null,
	    {
		text: 'Exportar pra PDF',
		onclick: function()
		{
		    this.exportChart({
			type: 'application/pdf'
		    });
		}
	    },
	    null
	    ]
	}
    }
  };

$( document ).ready(
    function()
    {
	initGraficoAcoes();
    }
);
    
    
function initGraficoAcoes()
{
    var graph = new Highcharts.Chart({
	  chart: {
	     renderTo: 'container-graph-acoes',
	     defaultSeriesType: 'line'
	  },
	  exporting: exportingChart,
	  credits: {
	     enabled: false
	  },
	  title: {
	     text: 'Downloads e Uploads por Hora',
	     x: -20
	  },
	  subtitle: {
	     text: 'Ações por hora',
	     x: -20
	  },
	  yAxis: {
	     title: {
		text: 'Valores'
	     }
	  },
	  tooltip: {
	     formatter: function() {
		   return '<b>'+ this.series.name +'</b><br/>'+
		   this.x +': '+ Highcharts.numberFormat( this.y, 0, '', '.');
	     }
	  },
	  series: [{
	     name: 'Downloads',
	     data: []
	  }, {
	     name: 'Uploads',
	     data: []
	  }]
   });
   
    $.ajax({
	url: baseUrl + '/admin/relatorio/grafico-acoes/',
	dataType: 'json',
	beforeSend: function() { graph.showLoading(); },
	success: function ( response )
	{
	    graph.hideLoading();

	    graph.xAxis[0].setCategories( response.horarios, false );
	    graph.series[0].setData( response.downloads, false );
	    graph.series[1].setData( response.uploads, false );

	    graph.redraw();
	},
	error: function() { graph.hideLoading(); }
    });
}

function initGraficoExtensoes()
{
    var graph = new Highcharts.Chart({
      chart: {
         renderTo: 'container-graph-extensoes',
         margin: [50, 0, 0, 0]
      },
      exporting: exportingChart,
      credits: {
	 enabled: false
      },
      title: {
         text: 'Downloads e Uploads por extensão'
      },
      subtitle: {
         text: 'Uploads círculo interno e Downloads círculo externo'
      },
      tooltip: {
         formatter: function() {
            return '<b>'+ this.series.name +'</b><br/>'+ 
               this.point.name +': '+ this.y;
         }
      },
      series: [
      {
         type: 'pie',
         name: 'Uploads',
         size: '50%',
         innerSize: '20%',
         data: [],
         dataLabels: { enabled: false }
	}, {
         type: 'pie',
         name: 'Downloads',
         innerSize: '50%',
         data: [],
         dataLabels: { enabled: true }
      }
      ]
   });
   
    $.ajax({
	url: baseUrl + '/admin/relatorio/grafico-extensoes/',
	dataType: 'json',
	beforeSend: function() { graph.showLoading(); },
	success: function ( response )
	{
	    graph.hideLoading();

	    graph.series[0].setData( response.uploads, false );
	    graph.series[1].setData( response.downloads, false );

	    graph.redraw();
	},
	error: function() { graph.hideLoading(); }
    });
}

function initGraficoCategorias()
{
    var graph = new Highcharts.Chart({
      chart: {
         renderTo: 'container-graph-categorias',
         margin: [50, 0, 0, 0]
      },
      exporting: exportingChart,
      credits: {
	 enabled: false
      },
      title: {
         text: 'Downloads e Uploads por categorias'
      },
      subtitle: {
         text: 'Uploads círculo interno e Downloads círculo externo'
      },
      tooltip: {
         formatter: function() {
            return '<b>'+ this.series.name +'</b><br/>'+ 
               this.point.name +': '+ this.y;
         }
      },
      series: [
      {
         type: 'pie',
         name: 'Uploads',
         size: '50%',
         innerSize: '20%',
         data: [],
         dataLabels: { enabled: false }
	}, {
         type: 'pie',
         name: 'Downloads',
         innerSize: '50%',
         data: [],
         dataLabels: { enabled: true }
      }
      ]
   });
   
   $.ajax({
	url: baseUrl + '/admin/relatorio/grafico-categorias/',
	dataType: 'json',
	beforeSend: function() { graph.showLoading(); },
	success: function ( response )
	{
	    graph.hideLoading();

	    graph.series[0].setData( response.uploads, false );
	    graph.series[1].setData( response.downloads, false );

	    graph.redraw();
	},
	error: function() { graph.hideLoading(); }
    });
}

function initGraficoPerfil()
{
    var graph = new Highcharts.Chart({
      chart: {
	 renderTo: 'container-graph-perfil',
	 defaultSeriesType: 'column'
      },
      exporting: exportingChart,
      credits: {
	 enabled: false
      },
      title: {
	 text: 'Downloads e Uploads por perfis',
	 x: -20
      },
      yAxis: {
	 title: {
	    text: 'Valores'
	 }
      },
      tooltip: {
	 formatter: function() {
	       return '<b>'+ this.series.name +'</b><br/>'+
	       this.x +': '+ Highcharts.numberFormat( this.y, 0, '', '.');
	 }
      },
      series: [{
	 name: 'Downloads',
	 data: []
      },{
	 name: 'Uploads',
	 data: []
      }]
   });
   
   $.ajax({
	url: baseUrl + '/admin/relatorio/grafico-perfil/',
	dataType: 'json',
	beforeSend: function() { graph.showLoading(); },
	success: function ( response )
	{
	    graph.hideLoading();

	    graph.xAxis[0].setCategories( response.perfis, false );
	    graph.series[0].setData( response.downloads, false );
	    graph.series[1].setData( response.uploads, false );

	    graph.redraw();
	},
	error: function() { graph.hideLoading(); }
    });
}

function initGraficoBaixados()
{
    var graph = new Highcharts.Chart({
      chart: {
	 renderTo: 'container-graph-baixados',
	 defaultSeriesType: 'column'
      },
      legend: {
	  enabled: false
      },
      exporting: exportingChart,
      credits: {
	 enabled: false
      },
      title: {
	 text: 'Arquivos mais baixados no mês atual',
	 x: -20
      },
      yAxis: {
	 title: {
	    text: 'Valores'
	 }
      },
      tooltip: {
	 formatter: function() {
	       return '<b>'+ this.x +':</b>'+ Highcharts.numberFormat( this.y, 0, '', '.');
	 }
      },
      series: [{
	 name: 'Arquivos',
	 data: []
      }]
   });
   
   
    $.ajax({
	url: baseUrl + '/admin/relatorio/grafico-baixados/',
	dataType: 'json',
	beforeSend: function() { graph.showLoading(); },
	success: function ( response )
	{
	    graph.hideLoading();

	    graph.xAxis[0].setCategories( response.arquivos, false );
	    graph.series[0].setData( response.downloads, false );

	    graph.redraw();
	},
	error: function() { graph.hideLoading(); }
    });
}