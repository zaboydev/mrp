$(document).ready(function(){
	var baselink = $("#baselink").val();
	$(".number").keydown(function (e) {
		if ($.inArray(e.keyCode, [8, 9, 27, 13, 110, 190]) !== -1 ||
             // Allow: Ctrl+A, Command+A
             (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) || 
             // Allow: home, end, left, right, down, up
             (e.keyCode >= 35 && e.keyCode <= 40)) {
                 // let it happen, don't do anything
             return;
         }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 47 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
        	e.preventDefault();
        }
    });
	$(".number").keyup(function () {
		var value = $(this).val().replace(/\./g,'');
		arr = value.split('');
		if(value == ""){
			$(this).val("0");
			return
		}
		if ((arr.length > 1)&&(arr[0] == 0)){
			value = value.substring(1,arr.length);
		}
	 	//value = value.replace(/./g,'');
	 	if(Math.floor(value == value)  && $.isNumeric(value)){
	 		value = addCommas(value);
	 		$(this).val(value);
	 	} 
	 });
	$("#add_cot_form").submit(function(e){
		if(parseInt($("#hour").val()) == 0){
			e.preventDefault();
			toastr.options.timeOut = 10000;
			toastr.options.positionClass = 'toast-top-right';
			toastr.error( 'hour can not be 0' );
		}

	})
	function addCommas(nStr) {
		nStr += '';
		var x = nStr.split('.');
		var x1 = x[0];
		var x2 = x.length > 1 ? '.' + x[1] : '';
		var rgx = /(\d+)(\d{3})/;
		while (rgx.test(x1)) {
			x1 = x1.replace(rgx, '$1' + '.' + '$2');
		}

		return x1 + x2;
	}
	$('#add-modal').on('hidden.bs.modal', function () {
		$("input[type=text]").val("0");
		$("#hour").attr("readonly", false); 
		$("#hour").attr("class","form-control");

	})
	var isCot = $("#onCot").val() == 1 ? true : false;
	currentPage = 1;
	page_count = 1;
	itemCount = 1;
	itemsId = "";
	itemsNo = "";
	itemsValue = {};
	itemsRange1 = {};
	itemsRange2 = {};
	id_kategori = $("#id_kategori").val();

	isCot ? loadItemCot() : '';
	$("#datatable-form").attr("class","navbar-search");
	function loadItemCot(){
		var search = $("#datatable-search-box").val();
		$("#loadingScreen").attr("style","display:block")
		var start = (currentPage - 1) * $("#limit").val();
		$.ajax({
			type: "POST",
			url: baselink+'/itemCot',
			data:{"start":start,"length":$("#limit").val(),"id_kategori":id_kategori,"search":search},
			cache: false,
			success: function(response){
				var data = jQuery.parseJSON(response);
				page_count = data.page_count;
				
				items = data.data;
				if(itemCount != data.dataCount){
					itemCount = data.dataCount;
					set();					
				}
				parseList(items);
				var limit = $("#limit").val();
				showing = ((currentPage - 1) * limit) + 1;
				$("#bottomLabel").html('Showing '+showing+' to '+((currentPage * limit) < itemCount ? (currentPage * limit) : itemCount)+' of '+itemCount+' ('+page_count+' Pages)')
			},
			error: function (xhr, ajaxOptions, thrownError) {
				console.log(xhr.status);
				console.log(xhr.responseText);
				console.log(thrownError);
			}
		});		
	}
	function set(){
		$('#paginContent').html('');
		$('#paginContent').html('<ul class="pagination" id="pagination"></ul>');
		if(page_count > 0){
			$('#pagination').twbsPagination({
				totalPages: page_count,
				visiblePages: 5,
				onPageClick: function (event, page) {

				}
			}).on('page', function (event, page) {
				currentPage = page;

				loadItemCot();
			});
		}
	}
	function parseList(data){
		$("#listView").html("");
		var no = (currentPage - 1) * $("#limit").val();
		$.each(data,function(i,item){
			no++;
			var text = '<tr>'+
                		// '<td style="text-align: center;"><input type="checkbox" id="cb_'+item.id+'" data-txt="txt_'+item.id+'" data-id="'+item.id+'" name="" style="display: inline;"></td>'+
				'<td style="text-align: center;"><input type="checkbox" id="cb_' + item.id + '" data-txt="txt_' + item.id + '" data-range1="range1_' + item.id + '" data-range2="range2_' + item.id + '" data-id="' + item.id+'" name="" style="display: inline;"></td>'+
                		'<td>'+no+'</td>'+
                		'<td>'+item.part_number+'</td>'+
                		'<td>'+item.description+'</td>'+
                		'<td>'+item.unit+'</td>'+
				'<td><input type="text" ' + ($("#id_kelipatan").val() === "0" ? "" : "class='number' readonly") + 'class="number" id="txt_' + item.id + '" data-type="txt" data-cb="cb_' + item.id + '" data-id="' + item.id+'" value="'+($("#id_kelipatan").val() === "0" ? "Other" : "0")+'" style="width: 100%"></td>'+
				'<td><input type="text" ' + ($("#id_kelipatan").val() === "0" ? "" : "class='number' readonly") + 'class="number" id="range1_' + item.id + '" data-type="range1" data-cb="cb_' + item.id + '" data-id="' + item.id+'" style="width: 100%" value="1"></td>'+
				'<td><input type="text" ' + ($("#id_kelipatan").val() === "0" ? "" : "class='number' readonly") + 'class="number" id="range2_' + item.id + '" data-type="range2" data-cb="cb_' + item.id + '" data-id="' + item.id+'" style="width: 100%" value="12"></td>'+
                		'</tr>';
                		$("#listView").append(text);
			if (itemsValue[item.id] != undefined){
				$("#cb_" + item.id).prop('checked',true);
				$("#txt_" + item.id).val(itemsValue[item.id]);
				$("#range1_" + item.id).val(itemsRange1[item.id]);
				$("#range2_" + item.id).val(itemsRange2[item.id]);
                		}

                	})
		$("#loadingScreen").attr("style","display:none")
	}
	$("#listView").on('click','input[type=checkbox]',function(){
		if($(this).prop('checked')){ 
			itemsId +="|"+$(this).attr('data-id')+",";
			itemsNo += "|" + $(this).attr('data-no') + ",";
			itemsValue[$(this).attr('data-id')] = $("#"+$(this).attr('data-txt')).val();
			itemsRange1[$(this).attr('data-id')] = $("#"+$(this).attr('data-range1')).val();
			itemsRange2[$(this).attr('data-id')] = $("#"+$(this).attr('data-range2')).val();
			console.log(itemsId);
			console.log(itemsValue);
			console.log(itemsRange1);
			console.log(itemsRange2);
		}else{ 
			itemsId = itemsId.replace("|"+$(this).attr('data-id')+",","");
			itemsNo = itemsNo.replace("|" + $(this).attr('data-id') + ",", "");
			delete itemsValue[$(this).attr('data-id')];
			delete itemsRange1[$(this).attr('data-id')];
			delete itemsRange2[$(this).attr('data-id')];
			console.log(itemsId);
			console.log(itemsValue);
			console.log(itemsRange1);
			console.log(itemsRange2);
		}		
	})
	$("#listView").on('keydown','.number',function(e){
		if ($.inArray(e.keyCode, [8, 9, 27, 13, 110, 190]) !== -1 ||
             // Allow: Ctrl+A, Command+A
             (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) || 
             // Allow: home, end, left, right, down, up
             (e.keyCode >= 35 && e.keyCode <= 40)) {
                 // let it happen, don't do anything
             return;
         }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 47 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
        	e.preventDefault();
        }
    })
	$("#listView").on('keyup','.number',function(e){
		var value = $(this).val().replace(/\./g,'');
		arr = value.split('');
		if(value == ""){
			$(this).val("0");
			return
		}
		if ((arr.length > 1)&&(arr[0] == 0)){
			value = value.substring(1,arr.length);
		}
	 	//value = value.replace(/./g,'');
	 	if(Math.floor(value == value)  && $.isNumeric(value)){
	 		value = addCommas(value);
	 		if($(this).data('type') == "txt"){
	 			if(itemsValue[$(this).attr('data-id')] != undefined){
	 				itemsValue[$(this).attr('data-id')] = value;

	 			}
	 		}
	 		if($(this).data('type') == "range1"){
	 			if(itemsRange1[$(this).attr('data-id')] != undefined){
	 				itemsRange1[$(this).attr('data-id')] = value;
	 				
	 			}
	 		}
	 		if($(this).data('type') == "range2"){
	 			if(itemsRange2[$(this).attr('data-id')] != undefined){
	 				itemsRange2[$(this).attr('data-id')] = value;
	 				
	 			}
	 		}
	 		$(this).val(value);
	 	} 
	 });
	$("#btn-save-data").click(function(){
		var err = 0;
		if (confirm('Are you sure want to save this COT and sending email? Continue?')){
			if(Object.keys(itemsValue).length === 0){
				toastr.options.timeOut = 10000;
				toastr.options.positionClass = 'toast-top-right';
				toastr.error( 'Empty Data' );	
			}else {
				for (var key in itemsValue) {
		 			// skip loop if the property is from prototype
		 			if (!itemsValue.hasOwnProperty(key)) continue;

		 			var obj = itemsValue[key];
		 			if(obj === "0"){
		 			    toastr.options.timeOut = 10000;
		 			    toastr.options.positionClass = 'toast-top-right';
		 			    toastr.error( obj.info, 'There are 0 value on your selected item standard value' );
		 			    err++;
		 			    break;

		 			}
		 		}
		 		if(err === 0){
		 			sendData();
		 		}
		 	}
		}
		

	 	});
	function sendData(){
		var standardQuantity = JSON.stringify(itemsValue);
		var range1 = JSON.stringify(itemsRange1);
		var range2 = JSON.stringify(itemsRange2);
		hour = $("#hour").val();
		year = $("#year").val();
		kelipatan = $("#kelipatan").val();
		id_kelipatan = $("#id_kelipatan").val();
		key = itemsId;
		$("#loadingScreen").attr("style","display:block")
		$.ajax({
			type: "POST",
			url: baselink+'/saveCot',
			data:{"range1":range1,"range2":range2,"standardQuantity":standardQuantity,"hour":hour,"year":year,"kelipatan":kelipatan,"key":key,"id_kelipatan":id_kelipatan},
			cache: false,
			success: function(response){
				console.log(response);
				$("#loadingScreen").attr("style","display:none")
				var data = jQuery.parseJSON(response);
				if(data.status == "success"){
					window.location.href = baselink;
				}
			},
			error: function (xhr, ajaxOptions, thrownError) {
				console.log(xhr.responseText);
				$("#loadingScreen").attr("style","display:none")
				toastr.options.timeOut = 10000;
				toastr.options.positionClass = 'toast-top-right';
				toastr.error(thrownError);
			}
		});

	}
	$("#datatable-form").submit(function(e){
		e.preventDefault();
	});
	$("#datatable-search-box").keyup(function(){
		if(isCot){
			currentPage = 1;
			itemCount = 0;
			loadItemCot();
		}

	});
	$("#limit").change(function(){
		currentPage = 1;
		itemCount = 0;
		loadItemCot();
	})
});