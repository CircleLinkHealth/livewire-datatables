@unless($column['hidden'])
    <div class="relative table-cell h-12 overflow-hidden align-top"
         @if (isset($column['width']))style="width:{{ $column['width'] }}"@endif>
        @if($column['unsortable'])
            <div
                class="w-full h-full px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider flex items-center focus:outline-none @if($column['align'] === 'right') justify-end @elseif($column['align'] === 'center') justify-center @endif">
                <span class="inline ">{{ str_replace('_', ' ', $column['label']) }}</span>
            </div>
        @else
            <button wire:click="sort('{{ $index }}')"
                    class="w-full h-full px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider flex items-center focus:outline-none @if($column['align'] === 'right') justify-end @elseif($column['align'] === 'center') justify-center @endif">
                <span class="inline ">{{ str_replace('_', ' ', $column['label']) }}</span>
                <span class="inline text-xs text-blue-400">
                    @if(! isset($this->multisort) || $this->multisort === false)
                        @if(isset($sort[0]) && $this->getIndexFromValue($sort[0]) == $index && ($direction = $this->getColumnDirection($sort[0])))
                            @if($direction === 'asc')
                                <x-icons.chevron-up wire:loading.remove class="h-6 w-6 text-green-600 stroke-current"/>
                            @endif
                            @if($direction === 'desc')
                                <x-icons.chevron-down wire:loading.remove
                                                      class="h-6 w-6 text-green-600 stroke-current"/>
                            @endif
                        @endif
                    @elseif($this->multisort === true)
                        @foreach($sort as $value)
                            @if($this->getIndexFromValue($value) == $index && ($direction = $this->getColumnDirection($value)))
                                @if($direction === 'asc')
                                    <x-icons.chevron-up wire:loading.remove
                                                        class="h-6 w-6 text-green-600 stroke-current"/>
                                @endif
                                @if($direction === 'desc')
                                    <x-icons.chevron-down wire:loading.remove
                                                          class="h-6 w-6 text-green-600 stroke-current"/>
                                @endif
                            @endif
                        @endforeach
                    @endif
            </span>
            </button>
        @endif
    </div>
@endif
