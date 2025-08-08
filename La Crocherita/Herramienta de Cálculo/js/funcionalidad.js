function calcular() {
  var materiales = parseFloat(document.getElementById("materiales").value);
  var unidades = parseFloat(document.getElementById("unidades").value);
  var horas = parseFloat(document.getElementById("horas").value);
  
  if (isNaN(materiales) || isNaN(unidades) || isNaN(horas)) {
    Swal.fire({
      title: "Error",
      text: "Por favor, ingrese valores válidos.",
      icon: "error",
    });
    return;
  } else {
    var precio = (materiales * horas) * horas;
    
    // Mostrar el resultado en el elemento <output> con id "resultado"
    document.getElementById("resultado").textContent = "₡" + precio.toFixed(2);
    
    console.log("Precio calculado: " + precio);
    return precio; // Retornar el precio para uso externo
  }
}

document.getElementById("btnCalcular").addEventListener("click", function () {
  calcular();
});

