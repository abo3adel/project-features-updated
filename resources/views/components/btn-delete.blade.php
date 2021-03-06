@props([
    'url' => '',
    'to' => null,
    'id' => '',
])

<div class="inline-block" x-data="{
    removing: false,
    handle: function (ev) {
        if (ev.slug !== '{{$id}}' || this.removing) return;

        this.remove('{{$id}}');
        {{-- console.log('{{$id}}') --}}
    },
    remove: async function(slug) {        
        if (this.removing || !slug) return;
        this.removing = true;

        const res = await axios.delete('{{$url}}').catch(err => {});

        this.removing = false;

        if (!res || res.status !== 204) {
            $dispatch('toast', {type: 'error', text: '{{__("category.error")}}' });
            return;
        }

        $dispatch('toast', {type: 'success', text: '{{__("category.success")}}' });

        @if (isset($to))
            location.href = '{{$to}}';
        @else
            {{-- remove element from dom --}}
            const el = document.querySelector('#' + slug);
            el.remove();
        @endif
    },
}" x-on:popup-confirm.window="handle($event.detail)">
    <x-btn-with-spinner class="red" type="button" icon='fas-trash'
        desc='delete element {{ $id }}' busy='removing' x-on:click.prevent="$dispatch('popup', {slug: '{{$id}}'})">
        <span class="sm:hidden lg:inline-block">
            {{__('category.delete')}}
        </span>
    </x-btn-with-spinner>
</div>