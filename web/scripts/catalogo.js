'use strict'

$(document).ready(function ()
{
    $("select").click(function ()
    {
        $.ajax({
            type: "POST",
            url: "{{ path('mostrar_sesiones_ajax') }}",
            data: {
                diames: $(this).children("option:selected").val(),
                id: $(this).attr('id')
            },
            success: function(data)
            {
                $("#horarios").html(data);
            },
            error: function (data, xhr, status) 
            {
                alert("error "+status)
            }
        });
    });
});