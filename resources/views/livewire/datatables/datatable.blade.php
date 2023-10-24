<div class="container mx-auto pt-2">
    @if (method_exists($this, 'tableTitle'))
        <div class="{{ $titleClass ?? 'pb-1' }}">
            {{ $this->tableTitle() }}
        </div>
    @endif

    @if ($beforeTableSlot && (method_exists($this, 'showBeforeTableSlot') ? $this->showBeforeTableSlot() : true))
        @includeIf($beforeTableSlot)
    @endif

    <div @class([
        'relative pb-8',
        'flex flex-wrap' => $this->flexWrapTable ?? false
    ])>
        @if ($hideable)
            @include('livewire.datatables.components.table-filters')
        @endif

        @if ($this->searchableColumns()->count())
            @php($isMultiSort = isset($this->multisort) && $this->multisort === true && count($this->sort) >= 1)

            <div @class([
               'content-end py-6',
               'flex justify-between items-center' => $isMultiSort
            ])>
                @if ($isMultiSort)
                    <button
                        wire:loading.class="opacity-50"
                        wire:click="forgetSortSession"
                        class="
                            px-5 py-2 rounded-md bg-white text-xs leading-5 font-medium tracking-wider
                            hover:bg-gray-50 focus:outline-none
                        "
                    >
                        <div class="flex items-center text-sm leading-5 font-medium text-cool-gray-700">
                            {{ 'Reset '. Str::plural('Column', count($this->sort)) .' Sorting' }}
                        </div>
                    </button>
                @endif

                @include('livewire.datatables.components.search-box')
            </div>
        @endif

        @if ($this->activeFilters)
            <span class="text-xl text-blue-400 uppercase">
                @lang('Filter active')
            </span>
        @endif

        @if ($this->activeFilters)
            <button
                wire:click="clearAllFilters"
                class="
                    flex items-center px-3 text-xs font-medium tracking-wider text-red-500 uppercase
                    bg-white border border-red-400 space-x-2 rounded-md leading-4 hover:bg-red-200
                    focus:outline-none
                "
            >
                <span>{{ __('Reset') }}</span>
                <x-icons.x-circle class="m-2"/>
            </button>
        @endif

        @if (count($this->massActionsOptions))
            <div class="flex items-center justify-center space-x-1">
                <label for="datatables_mass_actions">{{ __('With selected') }}:</label>

                <select
                    wire:model.live="massActionOption"
                    class="
                        px-3 text-xs font-medium tracking-wider uppercase bg-white border border-green-400
                        space-x-2 rounded-md leading-4 focus:outline-none
                    "
                    id="datatables_mass_actions"
                >
                    <option value="">{{ __('Choose...') }}</option>

                    @foreach($this->massActionsOptions as $group => $items)
                        @if (!$group)
                            @foreach($items as $item)
                                <option wire:key="mass-options-not-group-items-{{ $item }}" value="{{ $item['value'] }}">{{ $item['label'] }}</option>
                            @endforeach
                        @else
                            <optgroup label="{{ $group }}">
                                @foreach($items as $item)
                                    <option wire:key="mass-options-group-items-{{ $item }}" value="{{ $item['value'] }}">{{ $item['label'] }}</option>
                                @endforeach
                            </optgroup>
                        @endif
                    @endforeach
                </select>

                <button
                    wire:click="massActionOptionHandler"
                    class="
                        flex items-center px-4 py-2 text-xs font-medium tracking-wider text-green-500 uppercase
                        bg-white border border-green-400 rounded-md leading-4 hover:bg-green-200 focus:outline-none
                    "
                    type="submit"
                    title="Submit"
                >
                    Go
                </button>
            </div>
        @endif

        @if ($exportable)
            <div
                x-data="{
                    init() {
                        window.livewire.on('startDownload', link => window.open(link, '_blank'))
                    }
                }"
            >
                <button
                    wire:click="export"
                    class="
                        flex items-center px-3 text-xs font-medium tracking-wider text-green-500
                        uppercase bg-white border border-green-400 space-x-2 rounded-md leading-4
                        hover:bg-green-200 focus:outline-none
                    "
                >
                    <span>{{ __('Export') }}</span>
                    <x-icons.excel class="m-2"/>
                </button>
            </div>
        @endif

        @if ($hideable === 'select')
            @include('datatables::hide-column-multiselect')
        @endif

        @foreach ($columnGroups as $name => $group)
            <button
                wire:key="column-group-group-name-{{ $name }}"
                wire:click="toggleGroup('{{ $name }}')"
                class="
                    px-3 py-2 text-xs font-medium tracking-wider text-green-500 uppercase bg-white
                    border border-green-400 rounded-md leading-4 hover:bg-green-200 focus:outline-none
                "
            >
                <span class="flex items-center h-5">
                    {{ isset($this->groupLabels[$name]) ? __($this->groupLabels[$name]) : __('Toggle :group', ['group' => $name]) }}
                </span>
            </button>
        @endforeach

        @includeIf($buttonsSlot)

        @if ($hideable === 'buttons')
            <div class="p-2 grid grid-cols-8 gap-2">
                @foreach($this->columns as $index => $column)
                    @if ($column['hideable'])
                        <button
                            wire:key="hideable-columns-{{ $index }}"
                            wire:click="toggle('{{ $index }}')"
                            @class([
                                'px-3 py-2 rounded text-white text-xs focus:outline-none',
                                'bg-blue-100 hover:bg-blue-300 text-blue-600' =>  $column['hidden'],
                                'bg-blue-500 hover:bg-blue-800'               => !$column['hidden'],
                            ])
                        >
                            {{ $column['label'] }}
                        </button>
                    @endif
                @endforeach
            </div>
        @endif

        <div
            wire:loading.class="opacity-50"
            @class([
                'rounded-lg shadow-lg bg-white max-w-screen overflow-hidden',
                'rounded-b-none'  => $complex || !$this->hidePagination,
                'border-blue-500' => $this->activeFilters,
                isset($this->tableSize) ? $this->tableSize : '',
            ])
        >
            <div class="flex flex-col">
                <div class="overflow-x-auto soft-scrollbar">
                    <table class="min-w-full divide-y divide-gray-300">
                        @unless($this->hideHeader)
                            <thead class="bg-gray-50">
                            <tr data-cy="data-table-header">
                                @foreach($this->columns as $index => $column)
                                    <th wire:key="header-column-{{ $index }}" scope="col" class="py-3 px-3 first:px-5 last:px-5 whitespace-nowrap">
                                        @if ($hideable === 'inline')
                                            @include('datatables::header-inline-hide', ['column' => $column, 'sort' => $sort, 'key' => "header-inline-hide-index-$index"])
                                        @elseif($column['type'] === 'checkbox')
                                            @unless($column['hidden'])
                                                <div @class([
                                                        'rounded text-white text-center',
                                                        'bg-orange-400' =>  count($selected),
                                                        'bg-gray-200'   => !count($selected),
                                                    ])>
                                                    {{ count($selected) }}
                                                </div>
                                            @endunless
                                        @else
                                            @include('datatables::header-no-hide', ['column' => $column, 'sort' => $sort])
                                        @endif
                                    </th>
                                @endforeach
                            </tr>
                            </thead>
                        @endunless

                        <tbody class="divide-y divide-gray-200">
                        <tr>
                            @foreach($this->columns as $index => $column)
                                @if ($column['hidden'])
                                    @if ($hideable === 'inline')
                                        <td wire:key="inline-column-{{ $index }}" class="w-5 overflow-hidden align-top bg-blue-100"></td>
                                    @endif
                                @elseif($column['type'] === 'checkbox')
                                    <td wire:key="checkbox-column-{{ $index }}">@include('datatables::filters.checkbox')</td>
                                @elseif($column['type'] === 'label')
                                    <td wire:key="label-column-{{ $index }}">
                                        {{ $column['label'] ?? '' }}
                                    </td>
                                @else
                                    <td>
                                        @isset($column['filterable'])
                                            @if ( is_iterable($column['filterable']) )
                                                <div wire:key="column-filterable-iterable-index-{{ $index }}">
                                                    @include('datatables::filters.select', ['index' => $index, 'name' => $column['label'], 'options' => $column['filterable']])
                                                </div>
                                            @else
                                                <div wire:key="column-filterable-not-iterable-index-{{ $index }}">
                                                    @include('datatables::filters.' . ($column['filterView'] ?? $column['type']), ['index' => $index, 'name' => $column['label']])
                                                </div>
                                            @endif
                                        @endisset
                                    </td>
                                @endif
                            @endforeach
                        </tr>

                        @foreach($this->results as $row)
                            <tr wire:key="results-row-{{ $row->id }}"
                                @class([
                                    'text-sm',
                                    '!border-t-0' => $loop->first,
                                    $this->rowClasses($row, $loop)
                                ])>
                                @foreach($this->columns as $column)
                                    <td wire:key="results-column-{{ $column['name'] }}" @class([
                                            'text-sm px-3 py-3 text-gray-500 first:px-5 last:px-5',
                                            'truncate'    => !$column['wrappable'],
                                            'text-right ' =>  $column['contentAlign'] === 'right',
                                            'text-center' =>  $column['contentAlign'] === 'center',
                                            $this->cellClasses($row, $column)
                                        ])>
                                        @if ($column['hidden'])
                                            @if ($hideable === 'inline')
                                                <span></span>
                                            @endif
                                        @elseif($column['type'] === 'checkbox')
                                            @include('datatables::checkbox', ['value' => $row->checkbox_attribute])
                                        @elseif($column['type'] === 'label')
                                            @include('datatables::label')
                                        @else
                                            {!! $row->{$column['name']} !!}
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach

                        @if($this->results->count() === 0)
                            <tr class="!border-t-0">
                                <td class="p-4 text-gray-500" colspan="12">
                                    No results found
                                </td>
                            </tr>
                        @endif

                        @if (
                            $this->results->count()
                            && isset($this->clhCustomColSum)
                            && $this->clhCustomColSum
                            && method_exists($this, 'getCellsCustomSum')
                        )
                            @php($sumPerColumn = $this->getCellsCustomSum($this->results->all()))

                            <tr>
                                <td>
                                    @include("livewire.datatables.components.{$this->sumBladeFileName()}", [
                                        'sumPerColumn'=> $sumPerColumn,
                                        'totalRowName'=> $this->totalRowName(),
                                    ])
                                </td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @unless($this->hidePagination)
            <div class="w-full bg-white border-t rounded-b-lg shadow">
                <div class="py-2 px-6 sm:flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="hidden sm:block mr-4">
                            <p class="text-sm text-gray-700">
                                <span class="font-medium">{{ 'Showing' . ' ' . $this->results->firstItem() }}</span>
                                <span class="font-medium">{{ 'to' .' ' . $this->results->lastItem() }}</span>
                                <span
                                    class="font-medium">{{ 'of' . ' ' . $this->results->total() . ' ' .  'results' }}</span>
                            </p>
                        </div>

                        <div class="relative inline-block text-left">
                            <select
                                name="perPage"
                                class="inline-flex justify-center pl-3 pr-10 w-full rounded-md border border-gray-300 shadow-sm py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-indigo-500"
                                wire:model.live="perPage"
                            >
                                @foreach(config('livewire-datatables.per_page_options', [ 10, 25, 50, 100 ]) as $per_page_option)
                                    <option wire:key="per-page-options-{{ $per_page_option }}" value="{{ $per_page_option }}">Show {{ $per_page_option }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    @if (count($this->results))
                        <div class="my-4 sm:my-0">
                            <div class="lg:hidden">
                                <span class="space-x-2">
                                    {{ $this->results->links('datatables::tailwind-simple-pagination') }}
                                </span>
                            </div>

                            <div class="hidden lg:flex justify-center">
                                <span>{{ $this->results->links('datatables::tailwind-pagination') }}</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>

    @if ($complex)
        <div
            class="bg-gray-50 px-4 py-4 rounded-b-lg rounded-t-none shadow-lg border-4 @if ($this->activeFilters) border-blue-500 @else border-transparent @endif @if ($complex) border-t-0 @endif">
            <livewire:complex-query
                :columns="$this->complexColumns"
                :persist-key="$this->persistKey"
                :saved-queries="method_exists($this, 'getSavedQueries') ? $this->getSavedQueries() : null"
            />
        </div>
    @endif

    @if ($afterTableSlot)
        <div class="mt-8">
            @include($afterTableSlot)
        </div>
    @endif
</div>
