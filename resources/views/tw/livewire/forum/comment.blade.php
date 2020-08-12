<div class="flex justify-between items-base py-3 my-3 md:mx-4">
<div class="mt-4 w-16 hidden md:block">
    <div class="bg-primary-500 font-semibold inline-block p-3 rounded-full text-white">
        {{str($avatar)->upper()->initials()}}
    </div>
</div>
<div class="mt-4 w-full">
    <form id="post-form">
        <div class="form-group">
            
            <textarea wire:model="content" placeholder="Comenta aqui" class=" w-full border border-gray-200 active:outline-none focus:outline-none focus:shadow-outline-blue p-3 rounded" name="content" id="post-content"></textarea>

            @error('content') 
            <span class="error">{{ $message }}</span> 
            @enderror

        </div>
        <div class="flex justify-end">
            <button wire:click="save" type="button" class="mt-4 py-2 px-4 bg-primary-500 rounded text-white">Send Answer</button>
        </div>
    </form>
</div>
</div>
