@unless($column['hidden'])
    @php($label = str_replace('_', ' ', $column['label']))

    <div
        class="relative bg-gray-50 overflow-hidden"
        @isset($column['width']) style="width:{{ $column['width'] }}" @endisset
        data-cy="data-table-header-cell"
    >
        @if (! $column['sortable'])
            <div class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider flex items-center focus:outline-none @if($column['headerAlign'] === 'right') justify-end @elseif($column['headerAlign'] === 'center') justify-center @endif">
                <span class="inline text-center">
                    {{ $label }}
                </span>
            </div>
        @else
            <button
                wire:key="not-sortable-button-{{ $index }}"
                data-cy="data-table-header-cell-button"
                wire:click="sortColumn('{{ $index }}')"
                @class([
                    'text-left relative text-left text-xs font-medium uppercase',
                    'tracking-wider flex items-center gap-x-2 focus:outline-none',
                    'text-cyan-600'  => $isOrdering = method_exists($this, 'highlightOrdered') && $this->highlightOrdered($label),
                    'text-gray-500'  => !$isOrdering,
                    'justify-end'    => $column['headerAlign'] === 'right',
                    'justify-center' => $column['headerAlign'] === 'center',
                ])
            >
                <span class="whitespace-pre">{{ $label }}</span>

                <span class="flex text-center text-xs">
                    @if (! isset($this->multisort) || $this->multisort === false)
                        @if (isset($sort[0]) && $this->getIndexFromValue($sort[0]) == $index && ($direction = $this->getColumnDirection($sort[0])))
                            @if ($direction === 'asc')
                                <x-icon class="h-5 w-5" name="sort-ascending" />
                            @elseif ($direction === 'desc')
                                <x-icon class="h-5 w-5" name="sort-descending" />
                            @endif
                        @endif
                    @else
                        @foreach($sort as $key => $value)
                            @if ($this->getIndexFromValue($value) == $index && ($direction = $this->getColumnDirection($value)))
                                @if ($direction === 'asc')
                                    <x-icon class="h-5 w-5" name="sort-ascending" />

                                    @if (count($this->sort) > 1)
                                        <div>{{ $key + 1 }}</div>
                                    @endif
                                @elseif ($direction === 'desc')
                                    <x-icon class="h-5 w-5" name="sort-descending" />

                                    @if (count($this->sort) > 1)
                                        <div>{{ $key + 1 }}</div>
                                    @endif
                                @endif
                            @endif
                        @endforeach
                    @endif
                </span>
            </button>
        @endif
    </div>
@endif
