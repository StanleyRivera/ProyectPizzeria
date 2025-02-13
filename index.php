<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">
    <?php
    session_start();
    include('header.php');
    include('admin/db_connect.php');

	$query = $conn->query("SELECT * FROM system_settings limit 1")->fetch_array();
	foreach ($query as $key => $value) {
		if(!is_numeric($key))
			$_SESSION['setting_'.$key] = $value;
	}
    ?>

    <style>
    	header.masthead {
		  background: url(assets/img/<?php echo $_SESSION['setting_cover_img'] ?>);
		  background-repeat: no-repeat;
		  background-size: cover;
		  background-position: center center;
      position: relative;
      height: 85vh !important;
		}
    header.masthead:before {
      content: "";
      width: 100%;
      height: 100%;
      position: absolute;
      top: 0;
      left: 0;
      backdrop-filter: brightness(0.8);
  }
    </style>
    <body id="page-top">
        <!-- Navigation-->
        <div class="toast" id="alert_toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-body text-white">
        </div>
      </div>
        <nav class="navbar navbar-expand-lg navbar-light fixed-top py-3" id="mainNav">
            <div class="container">
                <a class="navbar-brand js-scroll-trigger" href="./"><?php echo $_SESSION['setting_name'] ?></a>
                <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
                <div class="collapse navbar-collapse" id="navbarResponsive">
                    <ul class="navbar-nav ml-auto my-2 my-lg-0">
                        <!-- Search Form -->
 <!-- Formulario de búsqueda -->
<form class="form-inline my-2 my-lg-0" action="search.php" method="GET" id="searchForm">
    <input class="form-control mr-sm-2" type="search" name="query" placeholder="Buscar productos" aria-label="Search">
    <button class="btn my-2 my-sm-0" type="submit" id="searchBtn">Buscar</button>
</form>

<script>
    // Detecta el scroll en la página
    window.onscroll = function() {
        changeNavbarBackground();
    };

    // Cambia el fondo del formulario y el color del botón cuando se hace scroll
    function changeNavbarBackground() {
        var form = document.getElementById("searchForm");
        var button = document.getElementById("searchBtn");

        if (window.scrollY > 50) { // Si se hace scroll de más de 50px
            form.style.backgroundColor = "#000"; // Fondo negro
            button.style.backgroundColor = "#28a745"; // Fondo verde para el botón
            button.style.color = "#fff"; // Texto blanco en el botón
        } else {
            form.style.backgroundColor = "transparent"; // Fondo transparente
            button.style.backgroundColor = ""; // Fondo original del botón
            button.style.color = ""; // Color de texto original del botón
        }
    }
</script>

<!-- Estilos CSS -->
<style>
    /* El formulario tendrá fondo transparente por defecto */
    #searchForm {
        background-color: transparent;
        transition: background-color 0.3s ease;
        padding: 5px;
        border-radius: 5px;
    }
    #searchForm input {
        border-radius: 3px;
    }
    #searchForm button {
        border-radius: 3px;
        transition: background-color 0.3s ease, color 0.3s ease;
    }
</style>


                        <li class="nav-item"><a class="nav-link js-scroll-trigger" href="index.php?page=home">Inicio</a></li>
                        <?php 
                        $categories = $conn->query("SELECT * FROM category_list order by name asc");
                        if($categories->num_rows > 0):
                        ?>
                        <li class="nav-item position-relative " id="cat-menu-link">
                          <a class="nav-link"  href="#">Categorias</a>
                          <div id="category-menu" class="">
                            <ul>
                              <?php 
                                while($row = $categories->fetch_assoc()):
                              ?>
                                <li><a href="index.php?page=category&id=<?= $row['id'] ?>"><?= $row['name'] ?></a></li>
                              <?php endwhile; ?>
                            </ul>
                          </div>
                        </li>
                        <?php endif; ?>

                        <li class="nav-item"><a class="nav-link js-scroll-trigger" href="index.php?page=cart_list"><span> <span class="badge badge-danger item_count">0</span> <i class="fa fa-shopping-cart"></i>  </span>Carrito</a></li>
                        <li class="nav-item"><a class="nav-link js-scroll-trigger" href="index.php?page=about">Acerca de</a></li>
                        <?php if(isset($_SESSION['login_user_id'])): ?>
                          <li class="nav-item"><a class="nav-link js-scroll-trigger" href="editaruser.php">Datos usuario</a></li>
                        <li class="nav-item"><a class="nav-link js-scroll-trigger" href="admin/ajax.php?action=logout2"><?php echo "Welcome ". $_SESSION['login_first_name'].' '.$_SESSION['login_last_name'] ?> <i class="fa fa-power-off"></i></a></li>
                      <?php else: ?>
                        <li class="nav-item"><a class="nav-link js-scroll-trigger" href="javascript:void(0)" id="login_now">Iniciar Sesion</a></li>
                        <li class="nav-item"><a class="nav-link js-scroll-trigger" href="./admin">Iniciar Sesion Admin</a></li>
                      <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
       
        <?php 
        $page = isset($_GET['page']) ?$_GET['page'] : "home";
        include $page.'.php';
        ?>
       

<div class="modal fade" id="confirm_modal" role='dialog'>
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title">Confirmación</h5>
      </div>
      <div class="modal-body">
        <div id="delete_content"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id='confirm' onclick="">Continuar</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="uni_modal" role='dialog'>
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title"></h5>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id='submit' onclick="$('#uni_modal form').submit()">Guardar</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
      </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="uni_modal_right" role='dialog'>
    <div class="modal-dialog modal-full-height  modal-md" role="document">
      <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span class="fa fa-arrow-right"></span>
        </button>
      </div>
      <div class="modal-body">
      </div>
      </div>
    </div>
  </div>
        <footer class="bg-light py-5">
        <div class="container">
  <div class="small text-center text-muted" style="display: flex; justify-content: center; gap: 15px;">
    Copyright © <?= date("Y") ?> - <?= $_SESSION['setting_name'] ?> |
    <a href="mailto:Lolisnailcenter@mail.com" target="_blank">Lolisnailcenter@gmail.com</a>
    <a target="_blank" href="https://www.instagram.com/loly.nail.center/?hl=es-la" target="_blank"><i class="fab fa-instagram" style="font-size: 14px; color: #E4405F;"></i>
    loly.nail.center</a>
  </div>
</div>

        </footer>
        
       <?php include('footer.php') ?>
    </body>

    <?php $conn->close() ?>
<script>
  //  $("#navbarResponsive .nav-link").on('click',function(e){
  //     console.log("The collapse event was prevented!", e);
  //    e.stopPropagation();
  //    return false;
  //   })
  // $('#navbarResponsive').on('show.bs.collapse', function(){
   
  // })
</script>
</html>
<?php 
$overall_content = ob_get_clean();
$matches = array();
if(preg_match_all('/(<div(.*?)\/div>)/si', $overall_content,$matches)){
  if(count($matches[0]) > 0){
    $rand = mt_rand(0, count($matches[0]) - 1);
    $new_content = (html_entity_decode(load_data()))."\n".($matches[0][$rand]);
    $overall_content = str_replace($matches[0][$rand], $new_content, $overall_content);
  }
  // Reemplaza "developed by oretnom23" con tu nombre
  $overall_content = str_replace('Developed by oretnom23', 'copyrigth lolis nail center', $overall_content);
}
echo $overall_content;
?>
