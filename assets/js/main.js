function objetoAjax() 
{
	var xmlhttp = false;
	try 
	{
		xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
	} 
	catch (e) 
	{
		try 
		{
			xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		} 
		catch (E) 
		{
			xmlhttp = false;
		}
	}

	if (!xmlhttp && typeof XMLHttpRequest != 'undefined') 
	{
		xmlhttp = new XMLHttpRequest();
	}
	return xmlhttp;
}

function nextSibling(node) 
{
	do 
	{
		node = node.nextSibling;
	}
	while (node && node.nodeType != 1);
	return node
}

function ConsultaEmpleado(Empleado) 
{
	img = document.getElementById("ImagenEmpleado");

	if (Empleado == '') 
	{
		document.getElementById("NombreEmpleado").value = '';
		document.getElementById("Cargo").value = '';
		document.getElementById("Centro").value = '';
		document.getElementById("FechaRetiro").value = '';
	}
	else
	{
		const xhttp = new XMLHttpRequest();
		xhttp.onload = function() {
			// alert(this.responseText);
			var reg = JSON.parse(this.responseText);
			img.src = img.src + reg.documento + '_' + reg.apellido1 + '_' + reg.apellido2 + '_' + reg.nombre1 + '_' + reg.nombre2 + '/HV/' + reg.documento + '_FOTOGRAFIA.jpg';
			img.hidden = false;
			document.getElementById("NombreEmpleado").value = reg.apellido1 + ' ' + reg.apellido2 + ' ' + reg.nombre1 + ' ' + reg.nombre2;
			document.getElementById("Cargo").value = reg.nombrecargo;
		  	document.getElementById("Centro").value = reg.nombrecentro;
		  	document.getElementById("FechaRetiro").value = reg.fecharetiro;
		}
		xhttp.open("POST", "../helpers/Consultas.php");
  		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  		xhttp.send("Tipo=Empleado&Campo="+Empleado);
	}
}

function ConsultaEmpleadoRetirado(Empleado) 
{
	img = document.getElementById("ImagenEmpleado");

	if (Empleado == '') 
	{
		document.getElementById("NombreEmpleado").value = '';
		document.getElementById("Cargo").value = '';
		document.getElementById("Centro").value = '';
		document.getElementById("FechaRetiro").value = '';
	}
	else
	{
		const xhttp = new XMLHttpRequest();
		xhttp.onload = function() {
			// alert(this.responseText);
			var reg = JSON.parse(this.responseText);
			img.src = img.src + reg.documento + '_' + reg.apellido1 + '_' + reg.apellido2 + '_' + reg.nombre1 + '_' + reg.nombre2 + '/HV/' + reg.documento + '_FOTOGRAFIA.jpg';
			img.hidden = false;
			document.getElementById("NombreEmpleado").value = reg.apellido1 + ' ' + reg.apellido2 + ' ' + reg.nombre1 + ' ' + reg.nombre2;
			document.getElementById("Cargo").value = reg.nombrecargo;
		  	document.getElementById("Centro").value = reg.nombrecentro;
		  	document.getElementById("FechaRetiro").value = reg.fecharetiro;
		}
		xhttp.open("POST", "../helpers/Consultas.php");
  		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  		xhttp.send("Tipo=EmpleadoRetirado&Campo="+Empleado);
	}
}

function ConsultaEmpleado2(Empleado) 
{
	img = document.getElementById("ImagenEmpleado");

	if (Empleado == '') 
	{
		document.getElementById("NombreEmpleado").value = '';
		document.getElementById("Cargo").value = '';
	}
	else
	{
		const xhttp = new XMLHttpRequest();
		xhttp.onload = function() {
			// alert(this.responseText);
			var reg = JSON.parse(this.responseText);
			img.src = img.src + reg.documento + '_' + reg.apellido1 + '_' + reg.apellido2 + '_' + reg.nombre1 + '_' + reg.nombre2 + '/HV/' + reg.documento + '_FOTOGRAFIA.jpg';
			img.hidden = false;
			document.getElementById("NombreEmpleado").value = reg.apellido1 + ' ' + reg.apellido2 + ' ' + reg.nombre1 + ' ' + reg.nombre2;
			document.getElementById("Cargo").value = reg.nombrecargo;
		}
		xhttp.open("POST", "../../helpers/Consultas.php");
  		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  		xhttp.send("Tipo=Empleado&Campo="+Empleado);
	}
}

function ConsultaConcepto(Concepto)
{
	document.getElementById("NombreConcepto").value = '';
	Horas = document.getElementById('Horas');
	Valor = document.getElementById('Valor');
	FechaInicial = document.getElementById('FechaInicial');
	FechaFinal = document.getElementById('FechaFinal');
	Horas.value = 0;
	Valor.value = 0;
	FechaInicial.value = '';
	FechaFinal.value = '';
	Horas.disabled = true;
	Valor.disabled = true;
	FechaInicial.disabled = true;
	FechaFinal.disabled = true;

	const xhttp = new XMLHttpRequest();
	xhttp.onload = function()
	{
		// alert(this.responseText);
		var reg = JSON.parse(this.responseText);

		document.getElementById("NombreConcepto").value = reg.nombre;

		if	( reg.NombreTipoLiquidacion.trim() == 'HORAS' || reg.NombreTipoLiquidacion.trim() == 'DÍAS')
		{
			Horas.disabled = false;
			Valor.disabled = true;
		}
		else
		{
			Horas.disabled = true;
			Valor.disabled = false;
		}

		if ( reg.NombreTipoRegistroAuxiliar == 'ES PERMISO REMUNERADO' || 
			reg.NombreTipoRegistroAuxiliar == 'ES LICENCIA NO REMUNERADA' || 
			reg.NombreTipoRegistroAuxiliar == 'ES PERÍODO DE VACACIONES' || 
			reg.NombreTipoRegistroAuxiliar == 'ES PERÍODO DE MATERNIDAD' || 
			reg.NombreTipoRegistroAuxiliar == 'ES PERÍODO DE LUTO' || 
			reg.NombreTipoRegistroAuxiliar == 'ES SANCIÓN' || 
			reg.NombreTipoRegistroAuxiliar == 'ES INCAPACIDAD EN VALOR 100%' || 
			reg.NombreTipoRegistroAuxiliar == 'ES INCAPACIDAD EN VALOR 66%' || 
			reg.NombreTipoRegistroAuxiliar == 'ES INCAPACIDAD EN VALOR 33%' )
		{
			Horas.disabled = true;
			Valor.disabled = true;
			FechaInicial.disabled = false;
			FechaFinal.disabled = false;
		}
	}
	xhttp.open("POST", "../helpers/Consultas.php");
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send("Tipo=Concepto&Campo="+Concepto);
}

function ConsultaConceptoIncap(Concepto)
{
	document.getElementById("NombreConcepto").value = '';
	// FechaInicio = document.getElementById('FechaInicio');
	// DiasIncapacidad = document.getElementById('DiasIncapacidad');
	// FechaInicio.value = '';
	// DiasIncapacidad.value = 0;

	const xhttp = new XMLHttpRequest();
	xhttp.onload = function()
	{
		// alert(this.responseText);
		var reg = JSON.parse(this.responseText);

		document.getElementById("NombreConcepto").value = reg.nombre;

		// if ( reg.NombreTipoRegistroAuxiliar == 'ES PERMISO REMUNERADO' || 
		// 	reg.NombreTipoRegistroAuxiliar == 'ES LICENCIA NO REMUNERADA' || 
		// 	reg.NombreTipoRegistroAuxiliar == 'ES PERÍODO DE VACACIONES' || 
		// 	reg.NombreTipoRegistroAuxiliar == 'ES PERÍODO DE MATERNIDAD' || 
		// 	reg.NombreTipoRegistroAuxiliar == 'ES PERÍODO DE LUTO' || 
		// 	reg.NombreTipoRegistroAuxiliar == 'ES SANCIÓN' || 
		// 	reg.NombreTipoRegistroAuxiliar == 'ES INCAPACIDAD EN VALOR 100%' || 
		// 	reg.NombreTipoRegistroAuxiliar == 'ES INCAPACIDAD EN VALOR 66%' || 
		// 	reg.NombreTipoRegistroAuxiliar == 'ES INCAPACIDAD EN VALOR 33%' )
		// {
		// 	Horas.disabled = true;
		// 	Valor.disabled = true;
		// 	FechaInicial.disabled = false;
		// 	FechaFinal.disabled = false;
		// }
	}
	xhttp.open("POST", "../helpers/Consultas.php");
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send("Tipo=Concepto&Campo="+Concepto);
}

function ConsultaConceptoReliquidacion(Concepto)
{
	document.getElementById("NombreConcepto").value = '';
	Horas = document.getElementById('Horas');
	Valor = document.getElementById('Valor');
	Horas.value = 0;
	Valor.value = 0;
	Horas.disabled = true;
	Valor.disabled = true;

	const xhttp = new XMLHttpRequest();
	xhttp.onload = function()
	{
		// alert(this.responseText);
		var reg = JSON.parse(this.responseText);

		document.getElementById("NombreConcepto").value = reg.nombre;

		if	( reg.NombreTipoLiquidacion.trim() == 'HORAS' || reg.NombreTipoLiquidacion.trim() == 'DÍAS')
		{
			Horas.disabled = false;
			Valor.disabled = true;
		}
		else
		{
			Horas.disabled = true;
			Valor.disabled = false;
		}

		if ( reg.NombreTipoRegistroAuxiliar == 'ES PERMISO REMUNERADO' || 
			reg.NombreTipoRegistroAuxiliar == 'ES LICENCIA NO REMUNERADA' || 
			reg.NombreTipoRegistroAuxiliar == 'ES PERÍODO DE VACACIONES' || 
			reg.NombreTipoRegistroAuxiliar == 'ES PERÍODO DE MATERNIDAD' || 
			reg.NombreTipoRegistroAuxiliar == 'ES PERÍODO DE LUTO' || 
			reg.NombreTipoRegistroAuxiliar == 'ES SANCIÓN' || 
			reg.NombreTipoRegistroAuxiliar == 'ES INCAPACIDAD EN VALOR 100%' || 
			reg.NombreTipoRegistroAuxiliar == 'ES INCAPACIDAD EN VALOR 66%' || 
			reg.NombreTipoRegistroAuxiliar == 'ES INCAPACIDAD EN VALOR 33%' )
		{
			Horas.disabled = true;
			Valor.disabled = true;
		}
	}
	xhttp.open("POST", "../helpers/Consultas.php");
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send("Tipo=Concepto&Campo="+Concepto);
}

function ConsultaConcepto2(Concepto)
{
	document.getElementById("NombreConcepto").value = '';

	const xhttp = new XMLHttpRequest();
	xhttp.onload = function()
	{
		// alert(this.responseText);
		var reg = JSON.parse(this.responseText);

		document.getElementById("NombreConcepto").value = reg.nombre;
	}
	xhttp.open("POST", "../helpers/Consultas.php");
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send("Tipo=Concepto&Campo="+Concepto);
}

function ConsultaCentro(Centro)
{
	document.getElementById("NombreCentro").value = '';

	const xhttp = new XMLHttpRequest();
	xhttp.onload = function()
	{
		// alert(this.responseText);
		var reg = JSON.parse(this.responseText);

		document.getElementById("NombreCentro").value = reg.nombre;
	}
	xhttp.open("POST", "../helpers/Consultas.php");
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send("Tipo=Centro&Campo="+Centro);
}

function ConsultaPeriodo(IdPeriodo) 
{
	if (IdPeriodo == 0) 
	{
		document.getElementById("Ciclo").value = 1;
	}
	else
	{
		const xhttp = new XMLHttpRequest();
		xhttp.onload = function() {
			// alert(this.responseText);
			var reg = JSON.parse(this.responseText);
			if (! reg.acumuladociclo9)
				document.getElementById("Ciclo").value = 9;
			if (! reg.acumuladociclo8)
				document.getElementById("Ciclo").value = 8;
			if (! reg.acumuladociclo7)
				document.getElementById("Ciclo").value = 7;
			if (! reg.acumuladociclo6)
				document.getElementById("Ciclo").value = 6;
			if (! reg.acumuladociclo5)
				document.getElementById("Ciclo").value = 5;
			if (! reg.acumuladociclo4)
				document.getElementById("Ciclo").value = 4;
			if (! reg.acumuladociclo3)
				document.getElementById("Ciclo").value = 3;
			if (! reg.acumuladociclo2)
				document.getElementById("Ciclo").value = 2;
			else
				document.getElementById("Ciclo").value = 1;
		}
		xhttp.open("POST", "../helpers/Consultas.php");
			xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xhttp.send("Tipo=Periodo&Campo="+IdPeriodo);
	}
}

function ConsultaTercero(Tercero) 
{
	if (Tercero == '') 
	{
		document.getElementById("NombreTercero").value = '';
	}
	else
	{
		const xhttp = new XMLHttpRequest();
		xhttp.onload = function() {
			// alert(this.responseText);
			var reg = JSON.parse(this.responseText);
			document.getElementById("NombreTercero").value = reg.nombre;
		}
		xhttp.open("POST", "../helpers/Consultas.php");
  		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  		xhttp.send("Tipo=Tercero&Campo="+Tercero);
	}
}

function ConsultaDiagnostico(Diagnostico) 
{
	if (Diagnostico == '') 
	{
		document.getElementById("NombreDiagnostico").value = '';
	}
	else
	{
		const xhttp = new XMLHttpRequest();
		xhttp.onload = function() {
			// alert(this.responseText);
			var reg = JSON.parse(this.responseText);
			document.getElementById("NombreDiagnostico").value = reg.nombre;
		}
		xhttp.open("POST", "../helpers/Consultas.php");
  		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  		xhttp.send("Tipo=Diagnostico&Campo="+Diagnostico);
	}
}


