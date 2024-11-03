$(document).ready(function (){
    var funcion='';
    var id_usuario = $('#id_usuario').val();
    var edit=false;
    buscar_usuario(id_usuario);
    function buscar_usuario(dato){
        funcion='buscar_usuario';
        $.post('../controlador/UsuarioController.php',{dato,funcion},(response)=>{
            let nombre='';
            let apellidos='';
            let edad='';
            let dni='';
            let tipo='';
            let telefono='';
            let residencia='';
            let correo='';
            let sexo='';
            let adicional='';
            const usuario = JSON.parse(response);
            nombre+=`${usuario.nombre}`;
            apellidos+=`${usuario.apellidos}`;
            edad+=`${usuario.edad}`;
            dni+=`${usuario.dni}`;
            tipo+=`${usuario.tipo}`;
            telefono+=`${usuario.telefono}`;
            residencia+=`${usuario.residencia}`;
            correo+=`${usuario.correo}`;
            sexo+=`${usuario.sexo}`;
            adicional+=`${usuario.adicional}`;
            $('#nombre_us').html(nombre);
            $('#apellidos_us').html(apellidos);
            $('#edad').html(edad);
            $('#dni_us').html(dni);
            $('#us_tipo').html(tipo);
            $('#telefono_us').html(telefono);
            $('#residencia_us').html(residencia);
            $('#correo_us').html(correo);
            $('#sexo_us').html(sexo);
            $('#adicional_us').html(adicional);
        })
    }
    $(document).on('click','.edit',(e)=>{
        funcion='capturar_datos';
        edit=true;
        $.post('../controlador/UsuarioController.php',{funcion,id_usuario},(response)=>{
            const usuario = JSON.parse(response);
            $('#telefono').val(usuario.telefono);
            $('#residencia').val(usuario.residencia);
            $('#correo').val(usuario.correo);
            $('#sexo').val(usuario.sexo);
            $('#adicional').val(usuario.adicional);
        })
    });
    $('#form-usuario').submit(e=>{
        if(edit==true){
            let telefono=$('#telefono').val();
            let residencia=$('#residencia').val();
            let correo=$('#correo').val();
            let sexo=$('#sexo').val();
            let adicional=$('#adicional').val();
            funcion='editar_usuario';
            $.post('../controlador/UsuarioController.php',{id_usuario,funcion,telefono,residencia,correo,sexo,adicional},(response=>{
                if(response=='editado'){
                    $('#editado').show(); // Mostramos la alerta
                    setTimeout(function(){
                        $('#editado').hide(); // Ocultamos después de 2 segundos
                    }, 2000);
                    $('#form-usuario').trigger('reset');
                }
                edit=false;
                buscar_usuario(id_usuario);
            }))
        }
        else{
            $('#noeditado').show(); // Mostramos la alerta
            setTimeout(function(){
                $('#noeditado').hide(); // Ocultamos después de 2 segundos
            }, 2000);
            $('#form-usuario').trigger('reset');
        }
        e.preventDefault();
    });

    $(document).ready(function(){
        // Manejador para el botón de cambiar password
        $('#btnCambiarPassword').click(function(){
            $('#cambiocontra').modal('show');
        });
    
        // Asegurarnos que el modal se resetee cuando se cierre
        $('#cambiocontra').on('hidden.bs.modal', function () {
            $('#form-pass')[0].reset();
            $('#update').hide();
            $('#noupdate').hide();
        });
    
        // El resto de tu código existente...
        $('#form-pass').submit(function(e){
            e.preventDefault();
            let oldpass = $('#oldpass').val();
            let newpass = $('#newpass').val();
            funcion = 'cambiar_contra';
            
            $.post('../controlador/UsuarioController.php',{
                id_usuario,
                funcion,
                oldpass,
                newpass
            }, function(response){
                if(response=='update'){
                    $('#update').show();
                    setTimeout(function(){
                        $('#update').hide();
                        $('#cambiocontra').modal('hide');
                    }, 2000);
                } else {
                    $('#noupdate').show();
                    setTimeout(function(){
                        $('#noupdate').hide();
                    }, 2000);
                }
                $('#form-pass')[0].reset();
            });
        });
    });
})