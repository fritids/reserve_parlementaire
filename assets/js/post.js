$('#form_search_reserve').submit(function() {
	// $('.alert-error').slideUp();
	// $('.alert-success').slideUp();
	$('.loading').show();
	$.ajax({
		type: "POST",
		dataType: "json",
		url: path,
		data: $('#form_search_reserve').serialize(),
		success: function(data) {
			$('.loading').hide();
			if (data.status == 'error') {
				// $('.alert-error').html(data.message);
				// $('.alert-error').slideDown();
			} else {
			$('.loading').hide();
				if ((data.data.caption != 'undefined') && (data.data.caption != '')) {
					$('#table_caption').html(data.data.caption);
					$('#table_caption').show();
				}
				$('#table_body').html(data.data.table);
				$('#pagination_body').html(data.data.pagination);
				$('.pagination ul li').click(paginate_click);
			}
		}
	});
	return false;
});

$('#newCalc').click(function() {
	$(this).hide();
	$('#form_loyer').slideDown();
	$('.alert-success').slideUp();
	return false;
});

var paginate_click = function() {
	$('#pagination_form').val($(this).val());
	$('#form_search_reserve').submit();
	return false;
};

$('.pagination ul li').click(paginate_click);

$(".chzn-select").chosen({allow_single_deselect: true}).change(function() {
	$('#table_caption').hide();
	$('#pagination_form').val(0);
	$('#form_search_reserve').submit();
});

var communeCache = new Array();
var currentTerm = '';

$("#communes").ajaxChosen({
    type: 'POST',
    url: path+"/search/communes",
    dataType: 'json',
}, function (data) {
    var results = [];
    $.each(data.items, function (i, val) {
        results.push({ value: val, text: val });
    });
    return results;
});

// $('.chzn-select-ajax').autocomplete({
//   source: function( request, response ) {
//     $.ajax({
// 	  type: "POST",
//       url: path+"/search/communes",
//       dataType: "json",
//       data: "term="+request.term,
//       // beforeSend: function(){$('ul.chzn-results').empty();},
//       // success: function( data ) {
//       //   response( $.map( data, function( item ) {
//       //     $('ul.chzn-results').append('<li class="active-result">' + item.name + '</li>');
//       //   }));
//       // }
//     });
//   }
// });
