<?php 
session_start();
$page = basename($_SERVER['REQUEST_URI']);
$id = $_SESSION['id'];

$permisos = [
    70 => ['traslado','servicios'],
    81 => ['recepcion'],
    63 => ['traslado','servicios','recepcion'],
    40 => ['traslado','servicios','recepcion']
    
];
$imagenes =[
    63 => 'https://blog.provaltec.cl/intranet/storage/personal/PNpIJ3iGhkjk2CrNvTytZXsTEN5yUSyinh6x4qDD.png',
    81 => 'https://blog.provaltec.cl/intranet/storage/personal/ZWvMt76Du2PyZxSzwTgcU90ZD8oomYA9pKSzgDEI.jpg',
    70 => 'https://blog.provaltec.cl/intranet/storage/personal/gmYfGoU3iqxIVwGDVrOwS8u8Ua0YcuJSZuEufh9v.png',
    40 => 'https://blog.provaltec.cl/intranet/storage/personal/k8nfHAUG4tgHfaMGdPFFjFJL5cMadlRkTreVP5qN.png'
    ];
$imagen = $imagenes[$id] ?? [];
$usuario_permisos = $permisos[$id] ?? [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $titulo ?? 'App seguimiento' ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
</head>
<style>
    .logo{
        width: 280px;
    }
    @media screen and (max-width: 768px) {
       .logo{
        width: 180px;
    } 
    }
</style>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <a class="navbar-brand" href="dashboard.php">
            <img class="m-4 logo" src="https://sistema.provaltec.cl/global_assets/images/LOGO-PROVALTEC.png" alt="Provaltec Spa"> 
        </a>
        <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
            <div class="offcanvas-header">
                <img class="offcanvas-title logo" src="https://sistema.provaltec.cl/global_assets/images/LOGO-PROVALTEC.png" alt="Provaltec Spa"> 
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <ul class="navbar-nav mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $page === 'dashboard.php' ? 'active' : '' ?>" href="dashboard.php">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $page === 'productos.php' ? 'active' : '' ?>" href="productos.php">Productos</a>
                    </li>
                    <?php if (in_array('traslado', $usuario_permisos)): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $page === 'traslado.php' ? 'active' : '' ?>" href="traslado.php">Traslado</a>
                    </li>
                    <?php endif; ?>
            
                    <?php if (!in_array($id, [81])): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $page === 'servicios.php' ? 'active' : '' ?>" href="servicios.php">Enviar a Servicios</a>
                    </li>
                    <?php endif; ?>
            
                    <?php if (in_array('recepcion', $usuario_permisos)): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $page === 'recepcion.php' ? 'active' : '' ?>" href="recepcion.php">Recepción</a>
                    </li>
                    <?php endif; ?>
                    
                    
                </ul>
            </div>
        </div>

        <div class="d-flex align-items-center">
            <div class="nav-item dropdown me-3">
                <a class="nav-link" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="<?php echo $imagen?>" width="40" alt="Usuario" class="rounded-circle">
                </a>
                <ul class="dropdown-menu" style="left:-120px;">
                    <li class="dropdown-item"><?php echo ucwords(strtolower($_SESSION['nombre'])); ?></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="../auth/logout.php">Salir</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>
<main class="container">
    <?= $contenido ?>
</main>
</body>
</html>