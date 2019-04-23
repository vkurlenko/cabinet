<?php
//debug($data);
?>
<label class="checkbox">
    <input type="checkbox" class="ajax-checkbox" data-model="<?=$model_name?>" data-id="<?=$data->id?>" data-attr="<?=$attr?>" <?=$data->$attr ? 'checked' : ''?>>
    <div class="checkbox__text"></div>
</label>