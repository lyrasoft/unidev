{{-- Part of virtualset project. --}}
<?php
$packageName = $app->packageResolver->getAlias(\Lyrasoft\Unidev\UnidevPackage::class);
$defaultImage = isset($defaultImage) ? $defaultImage : $asset->path . '/' . $packageName . '/images/default-img.png';
?>
<div id="{{ $attrs['id'] }}-wrap">

    <div class="row">
        <div class="col-md-4">
            <img class="sid-preview img-responsive"
                src="{{ $attrs['value'] ? $attrs['value'] . '#' . uniqid() : $defaultImage }}"
                alt="Preview">
        </div>
        <div class="col-md-8">
            <div class="sid-area filedrag">
                <button class="btn btn-success btn-xs" type="button" onclick="$('{{ '#' . $attrs['id'] }}-selector').click();">Select File</button> or drop files here
                <img src="{{ $asset->path . '/' . $packageName }}/images/ajax-loader.gif" id="{{ $attrs['id'] . '-loader' }}" class="sid-loader" alt="Lading" style="display: none;">
            </div>
            <div class="checkbox checkbox-primary">
                <input type="checkbox" name="{{ $attrs['id'] }}-delete-image" class="sid-delete-image" />
                <label for="{{ $attrs['id'] }}-delete-image">Delete</label>
            </div>
        </div>
    </div>

    <div style="display: none;">
        <input type="file" id="{{ $attrs['id'] }}-selector" class="sid-selector cropit-image-input" style="display: none;" />
        <input type="text" id="{{ $attrs['id'] }}-data" class="sid-data" name="{{ $attrs['id'] }}-data" value="" />
    </div>

    {{-- Push this modal to page bottom --}}
    @assetTemplate('single-image-upload@' . $attrs['id'])
    <div class="modal fade sid-modal" id="{{ $attrs['id'] }}-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Crop image</h4>
                </div>
                <div class="modal-body">
                    <div id="{{ $attrs['id'] }}-cropper" class="sid-cropper">

                        <!-- preview image -->
                        <div class="cropit-image-preview-container center-block" style="width: {{ $attrs['width'] }}px; height: {{ $attrs['height'] }}px;">
                            <div class="cropit-image-preview" style="width: {{ $attrs['width'] }}px; height: {{ $attrs['height'] }}px;"></div>
                        </div>

                        <!-- This range input controls zoom -->
                        <div class="slider-wrapper text-center" style="margin-top: 25px;">
                            <span class="fa fa-picture-o small-image" style="font-size: 15px"></span>
                            <input type="range" class="cropit-image-zoom-input custom" style="width: 130px; display: inline;">
                            <span class="fa fa-picture-o large-image" style="font-size: 25px"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary sid-save-button">Save</button>
                </div>
            </div>
        </div>
    </div>
    @endTemplate()
</div>
