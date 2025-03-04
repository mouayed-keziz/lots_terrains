{{ $getChildComponentContainer()->render() }}

<pre class="text-sm text-gray-600">
    {{ json_encode($this->record->answers, JSON_PRETTY_PRINT) }}
</pre>
