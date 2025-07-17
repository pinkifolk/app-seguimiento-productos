<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>App seguimiento</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<style>
    .form-login{
        width: 450px;
    }
    @media screen and (max-width: 768px) {
       .form-login{
        width: 80%;
        } 
    }
</style>
<body>
    <div class="d-flex justify-content-center align-items-center" style="width:100vw; height:100vh;">
        <form id="loginForm" class="form-login"> 
            <img class="mb-4 logo" src="https://sistema.provaltec.cl/global_assets/images/LOGO-PROVALTEC.png" alt="Provaltec Spa" width="300"> 
            <div class="form-floating"> 
                <input type="text" class="form-control" id="usuario" name="usuario" placeholder="email@email.com"> 
                <label for="usuario">Email o Usuario</label> 
            <p class="text-danger fade" id="errorUser" style="font-size: 14px; margin:0 0 0 12px;">mensaje</p>
            </div> 
            <div class="form-floating mb-2"> 
                <i class="fa-solid fa-eye position-absolute top-50 end-0 translate-middle-y me-3" id="showPass" style="cursor:pointer;"></i>
                <i class="fa-solid fa-eye-slash position-absolute top-50 end-0 translate-middle-y me-3 d-none" id="hidePass" style="cursor:pointer;"></i>
                <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña"> 
                <label for="password">Contraseña</label> 
            <p class="text-danger fade" id="errorPass" style="font-size: 14px; margin:0 0 0 12px;">mensaje</p>
            </div>
             
            <button id="logeado" class="btn w-100 py-2" type="submit" style="background-color:#73A114;font-weight: 500; color:white;">Ingresar</button> 
            
        </form>
        
    </div>
</body>
<script>
    const passAlert = document.getElementById('errorPass')
    const userAlert = document.getElementById('errorUser')  
    
    const passwordInput = document.getElementById('password')
    const showPass = document.getElementById('showPass')
    const hidePass = document.getElementById('hidePass')
    
    document.getElementById('loginForm').addEventListener('submit', function(e){
        e.preventDefault()
        const formData = new FormData(this);
        fetch('auth/login.php',{
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if(data.success){
                    document.getElementById('logeado').textContent = 'Ingresando...'
                setTimeout(() => {
                    window.location.href = data.redirect
                },800)
            }else{
                
                if(data.tipo === 'user'){
                    userAlert.className = 'text-danger fade show'
                    userAlert.innerHTML = data.mensaje
                }else{
                    passAlert.className = 'text-danger fade show'
                    passAlert.innerHTML = data.mensaje
                }
            }
            
            
        })
        .catch(error => console.error('Error en fetch:', error))
    })
    showPass.addEventListener('click', function () {
      passwordInput.type = 'text'
      showPass.classList.add('d-none')
      hidePass.classList.remove('d-none')
    })

    hidePass.addEventListener('click', function () {
      passwordInput.type = 'password'
      hidePass.classList.add('d-none')
      showPass.classList.remove('d-none')
    })
    
    
</script>
</html>