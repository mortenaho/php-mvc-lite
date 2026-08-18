@php
    $form = $post ?? null;
@endphp

<label class="field {{ error('title') ? 'is-invalid' : '' }}">
    <span>Title</span>
    <input type="text" name="title" value="{{ old('title', $form->title ?? '') }}" required>
    @if(error('title'))
        <small>{{ error('title') }}</small>
    @endif
</label>

<label class="field {{ error('excerpt') ? 'is-invalid' : '' }}">
    <span>Excerpt</span>
    <input type="text" name="excerpt" value="{{ old('excerpt', $form->excerpt ?? '') }}" required>
    @if(error('excerpt'))
        <small>{{ error('excerpt') }}</small>
    @endif
</label>

<label class="field {{ error('body') ? 'is-invalid' : '' }}">
    <span>Body</span>
    <textarea name="body" rows="10" required>{{ old('body', $form->body ?? '') }}</textarea>
    @if(error('body'))
        <small>{{ error('body') }}</small>
    @endif
</label>
