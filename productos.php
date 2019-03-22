<?php
/*
Template Name: Page Productos */
get_header(); 
wp_reset_query();
$mainImage = get_field( "imagen_de_fondo" );
$subtitle = get_field( "subtitulo" );


?>

<section class="banner" style="background-image:url('<?php echo $mainImage;?>') !important;">
    <div class="container">
        <div class="row">
            <div class="col-6">
                <h1 class="uppercase"><?php the_title(); ?></h1>
                <h3 class="uppercase"><?php echo $subtitle; ?></h3>
            </div>
        </div>
    </div>
</section>
<style>

#search_result_container{
    margin-top:20px;
}
#product-category-menu ul{
    list-style-type: none;
    margin: 0;
    padding: 0;
    overflow: hidden;
    font-size:14px;
    font-weight:bold;
}
#product-category-menu ul li{
    float: left;
    padding:2px 0px 2px 40px;
    
}

.menu-bar{
    background:#F5BB00;
    
}
.menu-bar1{
	background: #E7E7E7;
}
.menu-bar li a{
    color:#fff; 
}

.menu-bar1 li a{
    color:#000; 
}


.menu-bar li .menu-bar1{
    background:#E7E7E7;
}
.menu-bar li .menu-bar2{
    display: none;
}

.menu-bar li a:hover, .menu-bar li a:visited, .menu-bar li a:link, a:active
{
    text-decoration: none;
}

.menu-bar1 li a:hover, .menu-bar1 li a:visited, .menu-bar li a:link, a:active
{
    text-decoration: none;
}
.underline { 
	text-decoration: underline !important; 
	text-decoration-style:;
}

}
</style>
<nav id="product-category-menu" >
   	<ul class="menu-bar" data-type="cat">
        <li><a href="###">HOMBRE</a></li>
        <li><a href="###">MUJER</a></li>
        <li><a href="###">NIÑO</a></li>
        <li><a href="###">ACCESORIOS</a></li>
    </ul>
    <ul class="menu-bar1" data-type="type">
     <li><a href="###">OPTICOS</a></li>
     <li><a href="###">LENTES DE SOL</a></li>
     <li><a href="###">CRISTALES Y TRATAMIENTOS</a></li>
    </ul>
    <ul class="menu-bar2" data-type="brand" id="brand-filter">
     
    </ul>
</nav>

<div class="row">
<div class="col-md-12 text-center">
<br>
    <h3 id="search-text" >RESULTADO DE LA BUSQUEDA</h3>
</div>
</div>
<div id="search_result_container" class="container" >
        
       <div class="row"></div>

</div>
<br>
<br>
<div  class="container">
 <div class="row">
    <div class="col-md-12" id="pagination-container"></div>
 </div>
</div>
<br>
<br>

<?php
get_footer(); ?>
