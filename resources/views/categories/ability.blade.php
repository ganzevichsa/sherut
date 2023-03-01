<div class="row item-ability">
    <div class="col-md-5">
        <div class="form-group">
            <label class="control-label">Ability</label>
            <div class="col-sm-12">
                <select name="ability_id[]" class="form-control">
                    @foreach(\App\Models\QuizMaterials::get() as $ability)
                        <option value="{{ $ability->id }}">{{ $ability->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label class="control-label">Ability priority</label>
            <div class="col-sm-12">
                <input type="text" name="ability_value[]" class="form-control">
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label class="control-label">Passage point (percent)</label>
            <div class="col-sm-12">
                <div class="input-group" style="display: flex;">
                    <div class="input-group-prepend" style="display: -ms-flexbox;
    display: flex;
    -ms-flex-align: center;
    align-items: center;
    padding: .375rem .75rem;
    margin-bottom: 0;
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.5;
    color: #495057;
    text-align: center;
    white-space: nowrap;
    background-color: #e9ecef;
    border: 1px solid #ced4da;
    border-radius: .25rem;">
                        <div class="input-group-text">%</div>
                    </div>
                    <input type="text" name="ability_percent[]" class="form-control">
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-1">
        <label style="color: #fff;"> Delete</label>
        <a href="javascript:;" class="btn btn-default delete-ability" data-dismiss="modal" style="margin-top: 5px;">X</a>
    </div>
</div>