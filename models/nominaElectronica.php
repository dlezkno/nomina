<?php
	$NE = array();

	$NE = array(
		"periodo" => array(
			"fechaLiquidacionInicio" => "string",
			"fechaLiquidacionFin" => "string",
			"fechaGen" => "string"
		),
		"informacionGeneral" => array(
			"periodoNomina" => "string",
			"tipoXML" => "string",
			"version" => "string"
		),
		"lugarGeneracionXML" => array(
			"pais" => "string",
			"departamentoEstado" => "string",
			"municipioCiudad" => "string",
			"idioma" => "string"
		),
		"empleador" => array(
			"razonSocial" => "string",
			"primerApellido" => "string",
			"segundoApellido" => "string",
			"primerNombre" => "string",
			"otrosNombres" => "string",
			"nit" => 0,
			"dv" => 0,
			"pais" => "string",
			"departamentoEstado" => "string",
			"municipioCiudad" => "string",
			"direccion" => "string"
		),
	);

	$NEarray() = array(
  		"trabajador" => array(
			"tipoTrabajador" => "string",
			"subTipoTrabajador" => "string",
			"altoRiesgoPension" => true,
			"tipoDocumento" => "string",
			"numeroDocumento" => "string",
			"primerApellido" => "string",
			"segundoApellido" => "string",
			"primerNombre" => "string",
			"otrosNombres" => "string",
			"lugarTrabajoPais" => "string",
			"lugarTrabajoDepartamentoEstado" => "string",
			"lugarTrabajoMunicipioCiudad" => "string",
			"lugarTrabajoDireccion" => "string",
			"salarioIntegral" => true,
			"tipoDeContrato" => "string",
			"sueldo" => 0,
			"codigoTrabajador" => "string",
			"tipoNota" => "string",
			"novedad" => true,
			"predecesor" => array(
				"cune" => "string",
				"fechaGen" => "string",
				"numero" => "string"
			),
			"fechaIngreso" => "string",
			"fechaRetiro" => "string",
			"tiempoLaborado" => "string",
			"nota" => "string",
			"fechasPagos" => array(
				array(
					"fechaPago" => "string"
				)
			),
			"tipoMoneda" => "string",
			"tasaRepresentativa" => 0,
			"email" => "string",
			"numeroSecuenciaXML" => array(
				"prefijo" => "string",
				"consecutivo" => 0,
				"numero" => "string"
			),
			"pago" => array(
				"forma" => "string",
				"metodo" => "string",
				"banco" => "string",
				"tipoCuenta" => "string",
				"numeroCuenta" => "string"
			),
			"devengados" => array(
				"basico" => array(
					"diasTrabajados" => 0,
					"sueldoTrabajado" => 0
				),
				"transporte" => array(
					array(
						"auxilioTransporte" => 0,
						"viaticoManuAlojS" => 0,
						"viaticoManuAlojNS" => 0
					)
				),
				"heDs" => array(
					"hed" => array(
						array(
							"horaInicio" => "string",
							"horaFin" => "string",
							"cantidad" => "string",
							"porcentaje" => "string",
							"pago" => 0
						)
					)
				),
				"heNs" => array(
					"hen" => array(
						array(
							"horaInicio" => "string",
							"horaFin" => "string",
							"cantidad" => "string",
							"porcentaje" => "string",
							"pago" => 0
						)
					)
				),
				"hrNs" => array(
					"hrn" => array(
						array(
							"horaInicio" => "string",
							"horaFin" => "string",
							"cantidad" => "string",
							"porcentaje" => "string",
							"pago" => 0
						)
					)
				),
				"heddFs" => array(
					"heddf" => array(
						array(
							"horaInicio" => "string",
							"horaFin" => "string",
							"cantidad" => "string",
							"porcentaje" => "string",
							"pago" => 0
						)
					)
				),
				"hrddFs" => array(
					"hrddf" => array(
						array(
							"horaInicio" => "string",
							"horaFin" => "string",
							"cantidad" => "string",
							"porcentaje" => "string",
							"pago" => 0
						)
					)
				),
				"hendFs" => array(
					"hendf" => array(
						array(
							"horaInicio" => "string",
							"horaFin" => "string",
							"cantidad" => "string",
							"porcentaje" => "string",
							"pago" => 0
						)
					)
				),
				"hrndFs" => array(
					"hrndf" => array(
						array(
							"horaInicio" => "string",
							"horaFin" => "string",
							"cantidad" => "string",
							"porcentaje" => "string",
							"pago" => 0
						)
					)
				),
				"vacaciones" => array(
					"vacacionesComunes" => array(
						array(
							"fechaInicio" => "string",
							"fechaFin" => "string",
							"cantidad" => 0,
							"pago" => 0
						)
					),
					"vacacionesCompensadas" => array(
						array(
							"cantidad" => 0,
							"pago" => 0
						)
					)
				),
				"primas" => array(
					"cantidad" => 0,
					"pago" => 0,
					"pagoNS" => 0
				),
				"cesantias" => array(
					"pago" => 0,
					"porcentaje" => 0,
					"pagoIntereses" => 0
				),
				"incapacidades" => array(
					"incapacidad" => array(
						array(
							"fechaInicio" => "string",
							"fechaFin" => "string",
							"cantidad" => 0,
							"tipo" => 0,
							"pago" => 0
						)
					)
				),
				"licencias" => array(
					"licenciaMP" => array(
						array(
							"fechaInicio" => "string",
							"fechaFin" => "string",
							"cantidad" => 0,
							"pago" => 0
						)
					),
					"licenciaR" => array(
						array(
							"fechaInicio" => "string",
							"fechaFin" => "string",
							"cantidad" => 0,
							"pago" => 0
						)
					),
					"licenciaNR" => array(
						array(
							"fechaInicio" => "string",
							"fechaFin" => "string",
							"cantidad" => 0
						)
					)
				),
				"bonificaciones" => array(
					"bonificacion" => array(
						array(
							"bonificacionS" => 0,
							"bonificacionNS" => 0
						)
					)
				),
				"auxilios" => array(
					"auxilio" => array(
						array(
							"auxilioS" => 0,
							"auxilioNS" => 0
						)
					)
				),
				"huelgasLegales" => array(
					"huelgaLegal" => array(
						array(
							"fechaInicio" => "string",
							"fechaFin" => "string",
							"cantidad" => 0
						)
					)
				),
				"otrosConceptos" => array(
					"otroConcepto" => array(
						array(
							"descripcionConcepto" => "string",
							"conceptoS" => 0,
							"conceptoNS" => 0
						)
					)
				),
				"compensaciones" => array(
					"compensacion" => array(
						array(
							"compensacionO" => 0,
							"compensacionE" => 0
						)
					)
				),
				"bonoEPCTVs" => array(
					"bonoEPCTV" => array(
						array(
						"pagoS" => 0,
						"pagoNS" => 0,
						"pagoAlimentacionS" => 0,
						"pagoAlimentacionNS" => 0
						)
					)
				),
				"comisiones" => array(
					"comision" => array(
						0
					)
				),
				"pagosTerceros" => array(
					"pagoTercero" => array(
						0
					)
				),
				"anticipos" => array(
					"anticipo" => array(
						0
					)
				),
				"dotacion" => 0,
				"apoyoSost" => 0,
				"teletrabajo" => 0,
				"bonifRetiro" => 0,
				"indemnizacion" => 0,
				"reintegro" => 0
			),
		"deducciones" => array(
			"salud" => array(
			"porcentaje" => 0,
			"deduccion" => 0
			),
			"fondoPension" => array(
			"porcentaje" => 0,
			"deduccion" => 0
			),
			"fondoSP" => array(
			"porcentaje" => 0,
			"deduccionSP" => 0,
			"porcentajeSub" => 0,
			"deduccionSub" => 0
			),
			"sindicato" => array(
			"porcentaje" => "string",
			"deduccion" => 0
			),
			"sanciones" => array(
			"sancion" => array(
				array(
				"sancionPublic" => 0,
				"sancionPriv" => 0
				)
			)
			),
			"libranzas" => array(
			"libranza" => array(
				array(
				"descripcion" => "string",
				"deduccion" => 0
				)
			)
			),
			"pagosTerceros" => array(
			"pagoTercero" => array(
				0
			)
			),
			"anticipos" => array(
			"anticipo" => array(
				0
			)
			),
			"otrasDeducciones" => array(
			"otraDeduccion" => array(
				0
			)
			),
			"pensionVoluntaria" => 0,
			"retencionFuente" => 0,
			"afc" => 0,
			"cooperativa" => 0,
			"embargoFiscal" => 0,
			"planComplementarios" => 0,
			"educacion" => 0,
			"reintegro" => 0,
			"deuda" => 0
		),
		"redondeo" => 0,
		"devengadosTotal" => 0,
		"deduccionesTotal" => 0,
		"comprobanteTotal" => 0
		)
		)
	);


array(
  "periodo" => array(
    "fechaLiquidacionInicio" => "string",
    "fechaLiquidacionFin" => "string",
    "fechaGen" => "string"
  ),
  "informacionGeneral" => array(
    "periodoNomina" => "string",
    "tipoXML" => "string",
    "version" => "string"
  ),
  "lugarGeneracionXML" => array(
    "pais" => "string",
    "departamentoEstado" => "string",
    "municipioCiudad" => "string",
    "idioma" => "string"
  ),
  "empleador" => array(
    "razonSocial" => "string",
    "primerApellido" => "string",
    "segundoApellido" => "string",
    "primerNombre" => "string",
    "otrosNombres" => "string",
    "nit" => 0,
    "dv" => 0,
    "pais" => "string",
    "departamentoEstado" => "string",
    "municipioCiudad" => "string",
    "direccion" => "string"
  ),
  "trabajador" => array(
    array(
      "tipoTrabajador" => "string",
      "subTipoTrabajador" => "string",
      "altoRiesgoPension" => true,
      "tipoDocumento" => "string",
      "numeroDocumento" => "string",
      "primerApellido" => "string",
      "segundoApellido" => "string",
      "primerNombre" => "string",
      "otrosNombres" => "string",
      "lugarTrabajoPais" => "string",
      "lugarTrabajoDepartamentoEstado" => "string",
      "lugarTrabajoMunicipioCiudad" => "string",
      "lugarTrabajoDireccion" => "string",
      "salarioIntegral" => true,
      "tipoDeContrato" => "string",
      "sueldo" => 0,
      "codigoTrabajador" => "string",
      "tipoNota" => "string",
      "novedad" => true,
      "predecesor" => array(
        "cune" => "string",
        "fechaGen" => "string",
        "numero" => "string"
      ),
      "fechaIngreso" => "string",
      "fechaRetiro" => "string",
      "tiempoLaborado" => "string",
      "nota" => "string",
      "fechasPagos" => array(
        array(
          "fechaPago" => "string"
        )
      ),
      "tipoMoneda" => "string",
      "tasaRepresentativa" => 0,
      "email" => "string",
      "numeroSecuenciaXML" => array(
        "prefijo" => "string",
        "consecutivo" => 0,
        "numero" => "string"
      ),
      "pago" => array(
        "forma" => "string",
        "metodo" => "string",
        "banco" => "string",
        "tipoCuenta" => "string",
        "numeroCuenta" => "string"
      ),
      "devengados" => array(
        "basico" => array(
          "diasTrabajados" => 0,
          "sueldoTrabajado" => 0
        ),
        "transporte" => array(
          array(
            "auxilioTransporte" => 0,
            "viaticoManuAlojS" => 0,
            "viaticoManuAlojNS" => 0
          )
        ),
        "heDs" => array(
          "hed" => array(
            array(
              "horaInicio" => "string",
              "horaFin" => "string",
              "cantidad" => "string",
              "porcentaje" => "string",
              "pago" => 0
            )
          )
        ),
        "heNs" => array(
          "hen" => array(
            array(
              "horaInicio" => "string",
              "horaFin" => "string",
              "cantidad" => "string",
              "porcentaje" => "string",
              "pago" => 0
            )
          )
        ),
        "hrNs" => array(
          "hrn" => array(
            array(
              "horaInicio" => "string",
              "horaFin" => "string",
              "cantidad" => "string",
              "porcentaje" => "string",
              "pago" => 0
            )
          )
        ),
        "heddFs" => array(
          "heddf" => array(
            array(
              "horaInicio" => "string",
              "horaFin" => "string",
              "cantidad" => "string",
              "porcentaje" => "string",
              "pago" => 0
            )
          )
        ),
        "hrddFs" => array(
          "hrddf" => array(
            array(
              "horaInicio" => "string",
              "horaFin" => "string",
              "cantidad" => "string",
              "porcentaje" => "string",
              "pago" => 0
            )
          )
        ),
        "hendFs" => array(
          "hendf" => array(
            array(
              "horaInicio" => "string",
              "horaFin" => "string",
              "cantidad" => "string",
              "porcentaje" => "string",
              "pago" => 0
            )
          )
        ),
        "hrndFs" => array(
          "hrndf" => array(
            array(
              "horaInicio" => "string",
              "horaFin" => "string",
              "cantidad" => "string",
              "porcentaje" => "string",
              "pago" => 0
            )
          )
        ),
        "vacaciones" => array(
          "vacacionesComunes" => array(
            array(
              "fechaInicio" => "string",
              "fechaFin" => "string",
              "cantidad" => 0,
              "pago" => 0
            )
          ),
          "vacacionesCompensadas" => array(
            array(
              "cantidad" => 0,
              "pago" => 0
            )
          )
        ),
        "primas" => array(
          "cantidad" => 0,
          "pago" => 0,
          "pagoNS" => 0
        ),
        "cesantias" => array(
          "pago" => 0,
          "porcentaje" => 0,
          "pagoIntereses" => 0
        ),
        "incapacidades" => array(
          "incapacidad" => array(
            array(
              "fechaInicio" => "string",
              "fechaFin" => "string",
              "cantidad" => 0,
              "tipo" => 0,
              "pago" => 0
            )
          )
        ),
        "licencias" => array(
          "licenciaMP" => array(
            array(
              "fechaInicio" => "string",
              "fechaFin" => "string",
              "cantidad" => 0,
              "pago" => 0
            )
          ),
          "licenciaR" => array(
            array(
              "fechaInicio" => "string",
              "fechaFin" => "string",
              "cantidad" => 0,
              "pago" => 0
            )
          ),
          "licenciaNR" => array(
            array(
              "fechaInicio" => "string",
              "fechaFin" => "string",
              "cantidad" => 0
            )
          )
        ),
        "bonificaciones" => array(
          "bonificacion" => array(
            array(
              "bonificacionS" => 0,
              "bonificacionNS" => 0
            )
          )
        ),
        "auxilios" => array(
          "auxilio" => array(
            array(
              "auxilioS" => 0,
              "auxilioNS" => 0
            )
          )
        ),
        "huelgasLegales" => array(
          "huelgaLegal" => array(
            array(
              "fechaInicio" => "string",
              "fechaFin" => "string",
              "cantidad" => 0
            )
          )
        ),
        "otrosConceptos" => array(
          "otroConcepto" => array(
            array(
              "descripcionConcepto" => "string",
              "conceptoS" => 0,
              "conceptoNS" => 0
            )
          )
        ),
        "compensaciones" => array(
          "compensacion" => array(
            array(
              "compensacionO" => 0,
              "compensacionE" => 0
            )
          )
        ),
        "bonoEPCTVs" => array(
          "bonoEPCTV" => array(
            array(
              "pagoS" => 0,
              "pagoNS" => 0,
              "pagoAlimentacionS" => 0,
              "pagoAlimentacionNS" => 0
            )
          )
        ),
        "comisiones" => array(
          "comision" => array(
            0
          )
        ),
        "pagosTerceros" => array(
          "pagoTercero" => array(
            0
          )
        ),
        "anticipos" => array(
          "anticipo" => array(
            0
          )
        ),
        "dotacion" => 0,
        "apoyoSost" => 0,
        "teletrabajo" => 0,
        "bonifRetiro" => 0,
        "indemnizacion" => 0,
        "reintegro" => 0
      ),
      "deducciones" => array(
        "salud" => array(
          "porcentaje" => 0,
          "deduccion" => 0
        ),
        "fondoPension" => array(
          "porcentaje" => 0,
          "deduccion" => 0
        ),
        "fondoSP" => array(
          "porcentaje" => 0,
          "deduccionSP" => 0,
          "porcentajeSub" => 0,
          "deduccionSub" => 0
        ),
        "sindicato" => array(
          "porcentaje" => "string",
          "deduccion" => 0
        ),
        "sanciones" => array(
          "sancion" => array(
            array(
              "sancionPublic" => 0,
              "sancionPriv" => 0
            )
          )
        ),
        "libranzas" => array(
          "libranza" => array(
            array(
              "descripcion" => "string",
              "deduccion" => 0
            )
          )
        ),
        "pagosTerceros" => array(
          "pagoTercero" => array(
            0
          )
        ),
        "anticipos" => array(
          "anticipo" => array(
            0
          )
        ),
        "otrasDeducciones" => array(
          "otraDeduccion" => array(
            0
          )
        ),
        "pensionVoluntaria" => 0,
        "retencionFuente" => 0,
        "afc" => 0,
        "cooperativa" => 0,
        "embargoFiscal" => 0,
        "planComplementarios" => 0,
        "educacion" => 0,
        "reintegro" => 0,
        "deuda" => 0
      ),
      "redondeo" => 0,
      "devengadosTotal" => 0,
      "deduccionesTotal" => 0,
      "comprobanteTotal" => 0
    )
  )
)