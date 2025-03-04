{{ $getChildComponentContainer()->render() }}

<pre class="text-sm text-red-300">
    {{ json_encode($this->record->answers, JSON_PRETTY_PRINT) }}
</pre>

<pre class="text-sm text-blue-300">
    {{ json_encode($this->record->media, JSON_PRETTY_PRINT) }}
</pre>
