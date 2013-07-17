jQuery('#displayedText').tooltip({
        placement: 'right',
        title: 'Título de la promoción. El mismo debe ser corto y efectivo dado que se muestra en la lista de promociones aledañas. Por ejemplo "Liquidación de Verano!!", "Descuento pago efectivo", etc',
      });
jQuery('#shortDescription').tooltip({
        placement: 'right',
        title: 'Este texto es un resúmen de la descripción larga de la promoción. Debe ser efectivo dado que es el que se muestra en la lista de promociones aledañas.',
      });
jQuery('#promoValue').tooltip({
        placement: 'right',
        title: 'Es el valor mostrado que representa el precio o el descuento a aplicar por la promoción.',
      });
jQuery('#valueType').tooltip({
        placement: 'right',
        title: 'Por defecto el valor mostrado en la promoción es un precio. Pero esta opción permite mostrar el valor como porcentaje. Esta hecho para promocionar descuentos.',
      });
jQuery('#valueSince').tooltip({
        placement: 'right',
        title: 'Muestra la palabra "desde" encima del valor. Sirve para promocionar rangos inferiores de precios o descuentos. Por ejemplo "Remeras desde $70"',
      });
jQuery('#imagePromo').tooltip({
        placement: 'left',
        title: 'Imagen del producto o cualquier otra imagen que el anunciante quiera mostrar en el detalle de la promoción.',
      });
jQuery('#promoCode').tooltip({
        placement: 'right',
        title: 'Código de la promoción. Es asignado automáticamente por el sistema y sirve para hacer refencia a las promociones cuando haya que individualizarlas.',
      });
jQuery('#promoType').tooltip({
        placement: 'right',
        title: 'Especifica si la promoción consiste en un producto o un servicio. Sirve para filtrar búsquedas.',
      });
jQuery('#starts').tooltip({
        placement: 'right',
        title: 'Día de inicio de la promoción. Los campos Inicio y Final son los que determinan el costo total del anuncio. Solo se facturan los días lunes a viernes.',
      });
jQuery('#ends').tooltip({
        placement: 'right',
        title: 'Día de finalización de la promoción. Los campos Inicio y Final son los que determinan el costo total del anuncio. Solo se facturan los días lunes a viernes.',
      });
jQuery('#quantity').tooltip({
        placement: 'right',
        title: 'Indica cuantas unidades quedan disponibles bajo los términos de la promoción. Es opcional si visualización en la promoción',
      });
jQuery('#longDescription').tooltip({
        placement: 'left',
        title: 'Descripción detallada de la promoción.',
      });
jQuery('#state').tooltip({
        placement: 'left',
        title: 'Dependiendo el estado de la promoción, la misma será mostrada o no en los dispositivos de los clientes. Sirve para desactivar la promoción sin tener que cambiarle las fechas de Inicio o Fin. No afecta al costo de la promoción que es determinado por los campos Inicio y Fin.',
      });
jQuery('#promoCost').tooltip({
        placement: 'left',
        title: 'Monto que se desea pagar por el anuncio por día. Este valor influye en la posición donde se mostrará el anuncio con respecto a los otros anuncios mostrados en los dispositivos de los usuarios finales.',
      });
jQuery('#visited').tooltip({
        placement: 'left',
        title: 'Cantidad de visitas recibidas a la promoción.',
      });