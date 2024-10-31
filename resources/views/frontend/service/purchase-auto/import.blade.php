{{Form::open(array('route'=>array('admin.game-item.import'),'method'=>'POST','enctype'=>"multipart/form-data"))}}
<div class="modal-header">
	<h5 class="modal-title" id="exampleModalLabel">
		Import dữ liệu
	</h5>
	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		<span aria-hidden="true">×</span>
	</button>
</div>
<div class="modal-body">
	{{-- parrent_id --}}
	<div class="form-group {{ $errors->has('game_provider_id')? 'has-danger':'' }}">
		<label class="form-control-label">Danh mục game:</label>

		<select id="group_id" name="group_id" class="form-control">
			<option value="">-- Tất cả danh mục --</option>
			{!!\App\Library\Helpers::buildMenuDropdownList($dataCategory,old('group_id')) !!}
		</select>

		@if($errors->has('game_provider_id'))
			<div class="form-control-feedback">{{ $errors->first('game_provider_id') }}</div>
		@endif
	</div>
	{{-- import-excel --}}
	<div class="form-group {{ $errors->has('import-excel')? 'has-danger':'' }}">
		<label class="form-control-label">Đường dẫn file:</label>
		<input type="file" class="form-control" name="import-excel" value=""/>
		@if($errors->has('import-excel'))
			<div class="form-control-feedback">{{ $errors->first('import-excel') }}</div>
		@endif
	</div>


</div>
<div class="modal-footer">
	<button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
	<button type="submit" class="btn btn-success m-btn m-btn--custom m-btn--icon">
		Xác nhận
	</button>
</div>
{{ Form::close() }}
<script>
	$(document).ready(function () {

        $('.price').mask('000,000,000,000,000', {reverse: true});
		//file input upload file
		$('.fileinput').fileinput();
		$(".attribute-box input[type='checkbox']").change(function () {

			//click children
			$(this).closest('li').find("input[type='checkbox']").prop('checked', this.checked);
			var is_checked = $(this).is(':checked');

		});


	});


   jQuery(document).ready(function ($) {
        for(name in CKEDITOR.instances)
        {
            CKEDITOR.instances[name].destroy(true);
        }
        $('.ckeditor_post').each(function(){
            CKEDITOR.replace( $(this).attr('id') );
        });
    })


    $('.btn-edit-tut').on('click', function(e){

        $(".tut_area").toggle();

    });
</script>

