<div>
<form id="post-form-{{$post->id}}">
    <div class="form-group">
        <textarea wire:model="comment" class="bg-white border border-gray-300 focus:outline-none focus:shadow-outline mt-4 px-3 py-2 rounded text-gray-800 w-full" max-length="100" style="height:200px;"></textarea>
    </div>
    <div>
        <button class="btn btn-default bg-red-500 text-white px-2 rounded" @click="edit=false" type="button" >Cancel</button>
        <button type="button" wire:click="update({{$post->id}})" @click="edit=false" class="btn btn-primary">Update</button>
    </div>
</form>
</div>