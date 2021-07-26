<div class="modal" id="organizationModal" role="dialog" aria-hidden="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" data-token="{{ csrf_token() }}" class="createClassicRow">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title">צור / ערוך ארגון</h4></div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="control-label">שֵׁם:</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" name="name">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">שם הבמאי:</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" name="director">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">טלפון במאי:</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" name="phone_director">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">טלפון:</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" name="phone">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">אימייל:</label>
                        <div class="col-sm-12">
                            <input type="email" class="form-control" name="email">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">אתר אינטרנט:</label>
                        <div class="col-sm-12">
                            <input type="email" class="form-control" name="website">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">סגור</button>
                    <button type="submit" class="btn btn-primary">שמור שינויים</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal" id="hrModal" role="dialog" aria-hidden="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" data-token="{{ csrf_token() }}" class="createClassicRow">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title">צור / ערוך עובד Hr</h4></div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="control-label">שֵׁם:</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" name="name">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">אִרגוּן:</label>
                        <div class="col-sm-12">
                            <select class="chosen-select" id="hrOrganizationsList" name="organization_id"
                                    data-url="{{ route('organizations.hr') }}">
                                <option></option>
                                @foreach(\App\Organization::all() as $organization)
                                    <option value="{{ $organization->id }}">{{ $organization->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">טלפון:</label>
                        <div class="col-sm-12">
                            <input type="number" class="form-control" name="phone">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">סגור</button>
                    <button type="submit" class="btn btn-primary">שמור שינויים</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal" id="categoryModal" role="dialog" aria-hidden="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" data-token="{{ csrf_token() }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title">צור / ערוך קטגוריה</h4></div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="control-label">שֵׁם:</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" name="name">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Video Url</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" name="video_url">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Images</label>
                        <div class="col-sm-12">
                            <div class="img-block" style="display: none;">

                            </div>
                            <input type="file" multiple class="form-control" name="images[]">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Text</label>
                        <div class="col-sm-12">
                            <textarea name="text" class="form-control"></textarea>
                        </div>
                    </div>

                    <!-- <div class="form-group">
                        <label class="control-label">Quest value</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" name="value">
                        </div>
                    </div> -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">סגור</button>
                    <button type="submit" class="btn btn-primary">שמור שינויים</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal" id="subCategoryModal" role="dialog" aria-hidden="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" data-token="{{ csrf_token() }}" class="createClassicRow">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title">צור / ערוך קטגוריית משנה</h4></div>
                <div class="modal-body">

                    <div class="form-group">
                        <label class="control-label">שֵׁם:</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" name="name">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">קטגוריה:</label>
                        <div class="col-sm-12">
                            <select class="chosen-select" id="SubcategoryCategoryList" name="category_id">
                                <option></option>
                                @foreach(\App\Category::all() as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Video Url</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" name="video_url">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Images</label>
                        <div class="col-sm-12">
                            <div class="img-block" style="display: none;">

                            </div>
                            <input type="file" multiple class="form-control" name="images[]">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Text</label>
                        <div class="col-sm-12">
                            <textarea class="form-control" name="text"></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Min. count priority</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" name="value">
                        </div>
                    </div>

                    <!-- <div class="form-group">
                        <label class="control-label">Priority</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" name="priority">
                        </div>
                    </div> -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">סגור</button>
                    <button type="submit" class="btn btn-primary">שמור שינויים</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal" id="areaModal" role="dialog" aria-hidden="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" data-token="{{ csrf_token() }}" class="createClassicRow">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title">צור / ערוך אזור</h4></div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="control-label">שֵׁם:</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" name="name">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">סגור</button>
                    <button type="submit" class="btn btn-primary">שמור שינויים</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal" id="schoolModal" role="dialog" aria-hidden="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" data-token="{{ csrf_token() }}" class="createClassicRow">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title">צור / ערוך אזור</h4></div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="control-label">שֵׁם:</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" name="name">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">סגור</button>
                    <button type="submit" class="btn btn-primary">שמור שינויים</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal" id="cityModal" role="dialog" aria-hidden="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" data-token="{{ csrf_token() }}" class="createClassicRow">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title">צור / ערוך אזור</h4></div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="control-label">שֵׁם:</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" name="name">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">אֵזוֹר:</label>
                        <div class="col-sm-12">
                            <select class="chosen-select" id="CityAreasList" name="area_id">
                                <option></option>
                                @foreach(\App\Area::all() as $area)
                                    <option value="{{ $area->id }}">{{ $area->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">סגור</button>
                    <button type="submit" class="btn btn-primary">שמור שינויים</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal" id="locationModal" role="dialog" aria-hidden="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" data-token="{{ csrf_token() }}" class="createClassicRow">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title">צור / ערוך מיקום</h4></div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="control-label">שֵׁם:</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" name="name">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">סגור</button>
                    <button type="submit" class="btn btn-primary">שמור שינויים</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal" id="addressModal" role="dialog" aria-hidden="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" data-token="{{ csrf_token() }}" class="createClassicRow">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title">צור / ערוך כתובת</h4></div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="control-label">שֵׁם:</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" name="name">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">סגור</button>
                    <button type="submit" class="btn btn-primary">שמור שינויים</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal" id="stageOfEducationModal" role="dialog" aria-hidden="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" data-token="{{ csrf_token() }}" class="createClassicRow">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title">צור / ערוך את שלב החינוך</h4></div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="control-label">שֵׁם:</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" name="name">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">סגור</button>
                    <button type="submit" class="btn btn-primary">שמור שינויים</button>
                </div>
            </form>
        </div>
    </div>
</div>
