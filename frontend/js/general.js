$(document).ready(function() {
    $("#logoutIcon").on("click", function() {
        Swal.fire({
            title: "Tem certeza que deseja sair?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Sim, sair",
            cancelButtonText: "Cancelar"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "/logout",
                    type: "POST",
                    dataType: "json",
                    success: function(data) {
                        if (data.status === "success") {
                            window.location.href = "/";
                        } else {
                            Swal.fire("Erro!", "Erro ao fazer logout. Tente novamente!", "error");
                        }
                    },
                    error: function() {
                        Swal.fire("Erro!", "Não foi possível processar sua solicitação.", "error");
                    }
                });
            }
        });
    });
});