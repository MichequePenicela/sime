<head>

<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/jquery/flatpickr.min.css">
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/global.css">

<style>

/* HEADER */

.dashboard-header{

background:#fff;
border-radius:14px;
padding:22px;

box-shadow:0 4px 20px rgba(0,0,0,.05);

margin-bottom:22px;

}


/* FILTER */

.filter-card{

border:none;

border-radius:14px;

box-shadow:0 3px 18px rgba(0,0,0,.05);

}


/* CARD */

.dashboard-card{

border:none;

border-radius:14px;

transition:.25s ease;

opacity:0;

transform:translateY(15px);

animation:fadeUp .5s forwards;

}


@keyframes fadeUp{

to{

opacity:1;

transform:translateY(0);

}

}


.dashboard-card:hover{

transform:translateY(-6px);

box-shadow:0 14px 28px rgba(0,0,0,.08);

}


/* ICON */

.dashboard-icon{

width:52px;

height:52px;

border-radius:14px;

display:flex;

align-items:center;

justify-content:center;

background:#f4f6f9;

}


/* NUMBER */

.counter{

font-size:28px;

font-weight:700;

}


/* LOADING */

.loading{

padding:90px;

text-align:center;

color:#6c757d;

}


/* skeleton */

.skeleton{

height:120px;

border-radius:14px;

background:

linear-gradient(

90deg,

#eee 25%,

#f5f5f5 37%,

#eee 63%

);

background-size:400% 100%;

animation:skeleton 1.4s ease infinite;

}


@keyframes skeleton{

0%{background-position:100% 50%}

100%{background-position:0 50%}

}

</style>

</head>


<div class="container-fluid py-4">

<!-- HEADER -->

<div class="dashboard-header">

<h4 class="fw-bold mb-1">

<i class="fas fa-chart-line text-primary me-2"></i>

Dashboard de Membros

</h4>

<small class="text-muted">

Visão geral estatística do ministério

</small>

</div>



<!-- FILTRO -->

<div class="card filter-card mb-4">

<div class="card-body">

<div class="row g-3 align-items-end">


<div class="col-md-4">

<label class="small text-muted">

<i class="fas fa-calendar-alt"></i>

Data inicial

</label>

<input

id="dataInicio"

value="01-01-2020"

class="form-control datepicker">

</div>



<div class="col-md-4">

<label class="small text-muted">

Data final

</label>

<input

id="dataFim"

value="<?= date('Y-m-d') ?>"

class="form-control datepicker">

</div>



<div class="col-md-2">

<button

id="btnFiltrar"

class="btn btn-primary w-100">

<i class="fas fa-filter"></i>

Aplicar Filtro

</button>

</div>


</div>

</div>

</div>



<!-- DASHBOARD -->

<div class="row g-4" id="dashboardCards">

</div>

</div>



<script src="<?= BASE_URL ?>/assets/js/jquery/jquery-3.6.0.min.js"></script>

<script src="<?= BASE_URL ?>/assets/js/jquery/flatpickr.js"></script>


<script>

$(".datepicker").flatpickr({

dateFormat:"Y-m-d",

allowInput:true

});



$(function(){


/* CONTADOR CRESCENDO */

function animateCounter(el,target){

let start=0;

let duration=900;

let startTime=null;


function animation(current){

if(!startTime) startTime=current;

let progress=current-startTime;

let percent=Math.min(progress/duration,1);

let value=Math.floor(percent*target);

el.text(value.toLocaleString());

if(percent<1){

requestAnimationFrame(animation);

}

}

requestAnimationFrame(animation);

}



/* CARD */

function card(title,value,icon,color){

return`

<div class="col-sm-6 col-lg-4 col-xl-3">

<div class="card dashboard-card border-start border-4 border-${color}">

<div class="card-body">

<div class="d-flex justify-content-between align-items-center">


<div>

<div class="text-muted small">

${title}

</div>

<div

class="counter"

data-value="${value}">

0

</div>

</div>


<div class="dashboard-icon">

<i class="fas ${icon} fa-lg text-${color}"></i>

</div>


</div>

</div>

</div>

</div>

`;

}



/* SKELETON */

function skeleton(){

let html='';

for(let i=0;i<8;i++){

html+=`

<div class="col-sm-6 col-lg-4 col-xl-3">

<div class="skeleton"></div>

</div>

`;

}

return html;

}



function loadDatamember(){

$('#dashboardCards').html(skeleton());


$.getJSON(

'<?= BASE_URL ?>/membros/datamember',

{

inicio:$('#dataInicio').val(),

fim:$('#dataFim').val()

})

.done(function(response){


if(!response.success){

$('#dashboardCards').html(

'<div class="alert alert-warning">'+response.message+'</div>'

);

return;

}


const d=response.data;

let html='';


html+=card('Total de Membros',d.total,'fa-users','primary');

html+=card('Apagados',d.apagados,'fa-user-xmark','danger');

html+=card('Ativos',d.ativos,'fa-user-check','success');

html+=card('Abandonos',d.abandonou,'fa-user-slash','danger');

html+=card('Mudou-se',d.mudou,'fa-truck-moving','warning');

html+=card('Convertidos',d.convertidos,'fa-cross','info');

html+=card('Dominical',d.dominical,'fa-child','secondary');

html+=card('Jovens',d.jovens,'fa-user-graduate','primary');

html+=card('Mães',d.maes,'fa-person-dress','danger');

html+=card('Pais',d.pais,'fa-user-tie','dark');


$('#dashboardCards').html(html);



/* ANIMA CONTADOR */

$('.counter').each(function(){

animateCounter(

$(this),

parseInt($(this).data('value'))||0

);

});


})

.fail(function(){

$('#dashboardCards').html(

'<div class="alert alert-danger">Erro ao carregar dados</div>'

);

});

}



$('#btnFiltrar').on('click',loadDatamember);


loadDatamember();


});

</script>