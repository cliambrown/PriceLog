<x-app-layout>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            
            <div x-data="pricelog({
                    items: {{ $items->toJson() }},
                    item_ids: {{ $itemIDs->toJson() }},
                    entries: {{ $entries->toJson() }}
                })">
                
                <form class="flex gap-2 px-4 sm:px-0" x-on:submit.prevent="storeItem">
                    
                    <div class="relative grow">
                        
                        <button type="button" class="absolute top-0 right-0 flex items-center justify-center w-10 h-full text-gray-500" x-on:click="new_name = ''; updateFilter()">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                                <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm-1.72 6.97a.75.75 0 1 0-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 1 0 1.06 1.06L12 13.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L13.06 12l1.72-1.72a.75.75 0 1 0-1.06-1.06L12 10.94l-1.72-1.72Z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        
                        <x-text-input id="new_name" class="block w-full pr-12" type="text" name="new_name" x-model="new_name" autofocus x-on:input.debounce="updateFilter" />
                        
                    </div>
                    
                    <x-primary-button type="submit" x-bind:disabled="saving || !new_name.trim()">
                        Add
                    </x-primary-button>
                    
                </form>
                
                <div class="mt-6">
                    
                    <template x-if="!filtered_item_ids.length">
                        <div class="px-4 text-gray-700 sm:px-0">
                            No items
                            <span x-show="item_ids.length">found</span>
                        </div>
                    </template>
                    
                    <template x-for="itemID in filtered_item_ids" x-bind:key="itemID">
                        <div class="px-4 py-3 mt-2 overflow-hidden bg-white shadow-sm sm:rounded-lg" x-on:click="selectItem(itemID)">
                            
                            <div x-text="items[itemID].name" class="font-semibold text-purple-800"></div>
                            
                            <table class="w-full">
                                <template x-for="entryID in items[itemID].entry_ids.slice(0, items[itemID].entry_ids.length > 3 ? 2 : 3)" x-bind:key="entryID">
                                    <tr>
                                        <td class="pt-1 pr-3 text-right align-top whitespace-nowrap">
                                            <span class="text-gray-600 mr-0.5">$</span><span class="font-semibold" x-text="entries[entryID].price.toFixed(2)"></span>
                                            
                                        </td>
                                        <td class="w-full pt-1 align-top">
                                            <span class="mr-2" x-text="entries[entryID].location"></span>
                                            <span class="inline-block px-2 py-0.5 mr-2 text-xs font-semibold text-green-900 tracking-wide bg-green-100 rounded-full" x-show="entries[entryID].is_sale" x-cloak>SALE</span>
                                            <span class="text-xs text-gray-600 whitespace-nowrap" x-text="entries[entryID].seen_on_diff"></span>
                                        </td>
                                    </tr>
                                </template>
                                <tr x-show="items[itemID].entry_ids.length > 3" x-cloak>
                                    <td class="pt-1 text-gray-700" colspan="2">
                                        + <span x-text="items[itemID].entry_ids.length - 2"></span> more
                                    </td>
                                </tr>
                            </table>
                            
                        </div>
                    </template>
                </div>
                
                <div x-show="selected_item_id && selectedItem" x-cloak x-transition.opacity class="fixed inset-0 z-10 flex items-center justify-center w-screen p-2 bg-gray-900/50">
                    <template x-if="selected_item_id && selectedItem">
                        <div class="flex flex-col w-full max-w-2xl max-h-full bg-white rounded-lg" x-on:click.outside="selected_item_id = null">
                            
                            <div class="p-4 bg-gray-100 rounded-t-lg">
                                
                                <div x-show="!editing_selected_item_name" class="flex items-center justify-between h-10 gap-2">
                                    
                                    <div class="text-lg font-semibold text-purple-800 " x-text="selectedItem.name"></div>
                                    
                                    <button type="button" class="flex items-center justify-center size-10" x-on:click="selected_item_id = null">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                    
                                </div>
                                
                                <form x-show="editing_selected_item_name" x-cloak class="flex items-stretch justify-between h-10 gap-2" x-on:submit.prevent="updateSelectedItemName">
                                    
                                    <x-secondary-button type="button" x-bind:disabled="saving" class="flex justify-center w-10" :noSidePadding="true" x-on:click="toggleEditSelectedItemName(false)">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                        </svg>
                                    </x-secondary-button>
                                    
                                    <div class="relative grow">
                                        
                                        <button type="button" class="absolute top-0 right-0 flex items-center justify-center w-10 h-full text-gray-500" x-on:click="new_selected_item_name = ''" x-bind:disabled="saving">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                                                <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm-1.72 6.97a.75.75 0 1 0-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 1 0 1.06 1.06L12 13.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L13.06 12l1.72-1.72a.75.75 0 1 0-1.06-1.06L12 10.94l-1.72-1.72Z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                        
                                        <x-text-input id="new_selected_item_name" class="block w-full pr-12" type="text" name="new_selected_item_name" x-model="new_selected_item_name" />
                                        
                                    </div>
                                    
                                    <x-primary-button x-show="editing_selected_item_name" x-cloak type="submit" x-bind:disabled="saving || !new_selected_item_name.trim()" class="flex justify-center w-10" :noSidePadding="true">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                        </svg>
                                    </x-primary-button>
                                    
                                </form>
                                
                            </div>
                            
                            <div class="p-4 overflow-y-auto">
                                
                                <div x-show="!selectedItem.entry_ids.length" x-cloak class="text-gray-700">
                                    No entries
                                </div>
                                
                                <table class="w-full">
                                    
                                    <template x-for="entryID in selectedItem.entry_ids" x-bind:key="entryID">
                                        <tr x-on:click="editEntry(entryID)">
                                            
                                            <td class="py-2 pr-3 text-right align-top whitespace-nowrap">
                                                <div>
                                                    <span class="mr-1 text-gray-500">$</span><span class="text-xl font-semibold" x-text="entries[entryID].price.toFixed(2)"></span>
                                                </div>
                                                <div class="mt-1" x-show="entries[entryID].is_sale" x-cloak>
                                                    <span class="inline-block px-2 py-0.5 relative bottom-1 text-xs font-semibold text-green-900 tracking-wide bg-green-100 rounded-full">SALE</span>
                                                </div>
                                            </td>
                                            
                                            <td class="w-full py-2 align-top">
                                                <div class="text-xl" x-text="entries[entryID].location"></div>
                                                <div class="mt-1 text-sm text-gray-600">
                                                    <span x-text="entries[entryID].seen_on.toLocaleDateString('en-US', { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' })"></span>
                                                    (<span x-text="entries[entryID].seen_on_diff"></span>)
                                                </div>
                                                <div class="text-sm mt-0.5 whitespace-pre-wrap" x-show="entries[entryID].notes" x-cloak x-text="entries[entryID].notes"></div>
                                            </td>
                                            
                                        </tr>
                                    </template>
                                    
                                </table>
                                
                            </div>
                            
                            <div class="p-4 bg-gray-100 rounded-b-lg">
                                <div class="flex items-stretch justify-start h-10 gap-6">
                                    
                                    <x-danger-button type="button" class="flex justify-center w-10" x-bind:disabled="saving" :noSidePadding="true" x-on:click="deleteSelectedItem">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                          </svg>
                                    </x-danger-button>
                                    
                                    <x-secondary-button type="button" x-show="!editing_selected_item_name" class="flex justify-center w-10" :noSidePadding="true" x-on:click="toggleEditSelectedItemName(true)" x-bind:disabled="saving">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                        </svg>
                                    </x-secondary-button>
                                    
                                    <x-primary-button type="button" class="flex justify-center w-10 ml-auto" :noSidePadding="true" x-on:click="editNewEntry" x-bind:disabled="saving">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                        </svg>
                                    </x-primary-button>
                                    
                                </div>
                            </div>
                
                            <div x-show="selected_entry_id && selectedEntry" x-cloak x-transition.opacity class="fixed inset-0 z-20 flex items-center justify-center w-screen p-2 bg-gray-900/50">
                                <template x-if="selected_entry_id && selectedEntry">
                                    <form class="flex flex-col w-full max-w-xl max-h-full bg-white rounded-lg" x-on:click.outside="editEntry(null)" x-on:submit.prevent="saveEntry">
                                        
                                        <div class="p-4 bg-gray-100 rounded-t-lg">
                                            <div class="flex items-center justify-between h-10 gap-2">
                                                
                                                <div>
                                                    <div class="text-lg font-semibold text-green-800" x-text="selectedEntryTitle"></div>
                                                    <div class="text-sm">
                                                        Price of
                                                        <span x-text="selectedItem.name" class="font-semibold text-purple-800"></span>
                                                    </div>
                                                </div>
                                                
                                                <button type="button" class="flex items-center justify-center size-10" x-on:click="editEntry(null)" x-bind:disabled="saving">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                                
                                            </div>
                                        </div>
                                        
                                        <div class="p-4 overflow-y-auto">
                                            
                                            <div class="">
                                                <x-input-label for="new_entry_location" value="Location" />
                                                <x-text-input id="new_entry_location" class="block w-full pr-12 mt-1" type="text" name="new_entry_location" x-model="new_entry_location" />
                                            </div>
                                            
                                            <div class="flex items-start justify-start gap-8 mt-4">
                                                
                                                <div>
                                                    <x-input-label for="new_entry_price" value="Price" />
                                                    <div class="flex items-center justify-start gap-12">
                                                        <div class="relative">
                                                            <div class="absolute top-0 flex items-center justify-center w-6 h-full left-2">
                                                                <span>$</span>
                                                            </div>
                                                            <x-text-input id="new_entry_price" class="block w-full pl-8 mt-1 max-w-28" type="text" name="new_entry_price" x-model="new_entry_price" />
                                                        </div>
                                                        <label for="new_entry_is_sale" class="flex items-center">
                                                            <input id="new_entry_is_sale" type="checkbox" class="text-indigo-600 border-gray-300 rounded shadow-sm focus:ring-indigo-500" name="new_entry_is_sale" x-model="new_entry_is_sale">
                                                            <span class="text-sm text-gray-600 ms-2">Sale</span>
                                                        </label>
                                                    </div>
                                                </div>
                                                
                                            </div>
                                            
                                            <div class="mt-4"
                                                x-init="
                                                    datepicker = flatpickr($refs.datepicker_el, {
                                                        altInput: true,
                                                        altFormat: 'D, M j, Y',
                                                        inline: true,
                                                        onChange: (selectedDates, dateStr) => {
                                                            new_entry_seen_on = selectedDates[0];
                                                        },
                                                    });
                                                    datepicker.setDate(new_entry_seen_on);
                                                "
                                                >
                                                <div class="flex items-end justify-start gap-24">
                                                    <x-input-label for="new_entry_seen_on" value="Seen On" />
                                                    <button type="button" class="flex items-center gap-1 p-2 -m-2 text-sm font-semibold text-blue-800" x-on:click="seenOnToday">
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="relative size-4 top-px">
                                                            <path fill-rule="evenodd" d="M4 1.75a.75.75 0 0 1 1.5 0V3h5V1.75a.75.75 0 0 1 1.5 0V3a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2V1.75ZM4.5 6a1 1 0 0 0-1 1v4.5a1 1 0 0 0 1 1h7a1 1 0 0 0 1-1V7a1 1 0 0 0-1-1h-7Z" clip-rule="evenodd" />
                                                        </svg>
                                                        Today
                                                    </button>
                                                </div>
                                                <div>
                                                    <input type="text" name="new_entry_seen_on" id="new_entry_seen_on" class="w-full pr-12 my-1 rounded max-w-52" x-ref="datepicker_el" readonly>
                                                </div>
                                            </div>
                                            
                                            <div class="mt-4">
                                                <x-input-label for="new_entry_notes" value="Notes" />
                                                <textarea x-data x-autosize x-model="new_entry_notes" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                            </div>
                                            
                                        </div>
                                        
                                        <div class="p-4 bg-gray-100 rounded-b-lg">
                                            <div class="flex items-stretch justify-between h-10 gap-6">
                                                
                                                <x-danger-button type="button" x-show="selected_entry_id !== 'new'" x-cloak class="flex justify-center w-10" :noSidePadding="true" x-bind:disabled="saving" x-on:click="deleteSelectedEntry">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                      </svg>
                                                </x-danger-button>
                                    
                                                <x-primary-button type="submit" class="flex justify-center w-10 ml-auto" :noSidePadding="true" x-bind:disabled="saving || !canSaveEntry">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                                    </svg>
                                                </x-primary-button>
                                                
                                            </div>
                                        </div>
                                        
                                    </form>
                                </template>
                            </div>
                            
                        </div>
                    </template>
                </div>
                
            </div>
            
        </div>
    </div>
</x-app-layout>
