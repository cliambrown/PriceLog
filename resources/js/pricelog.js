export default (initState) => ({
  
  items: initState.items,
  item_ids: initState.item_ids,
  filtered_item_ids: [],
  entries: initState.entries,
  new_name: '',
  saving: false,
  
  init() {
    this.updateFilter();
    for (const key in this.entries) {
      this.entries[key].seen_on = dateFromYmd(this.entries[key].seen_on);
    }
  },
  
  selected_item_id: null,
  get selectedItem() {
    return this.items[this.selected_item_id];
  },
  new_selected_item_name: '',
  editing_selected_item_name: false,
  
  selected_entry_id: null,
  get selectedEntry() {
    if (this.selected_entry_id === 'new') return this.new_entry;
    return this.entries[this.selected_entry_id];
  },
  get selectedEntryTitle() {
    if (this.selected_entry_id === 'new') return 'New Entry';
    if (this.selectedEntry && this.selectedEntry.location) {
      return this.selectedEntry.location;
    }
    return 'Entry';
  },
  new_entry: {},
  new_entry_location: '',
  new_entry_is_sale: false,
  new_entry_price: null,
  new_entry_seen_on: null,
  new_entry_notes: null,
  datepicker: null,
  
  updateFilter() {
    const newItemName = simplifyString(this.new_name);
    let itemIDs = [];
    if (newItemName) {
      for (const itemID of this.item_ids) {
        if (this.items[itemID].filter_name.includes(newItemName)) {
          this.items[itemID].hidden = false;
          itemIDs.push(itemID);
        } else {
          this.items[itemID].hidden = true;
        }
      }
    } else {
      for (const itemID of this.item_ids) {
        this.items[itemID].hidden = false;
        itemIDs.push(itemID);
      }
    }
    this.filtered_item_ids = itemIDs;
  },
  
  storeItem() {
    if (this.saving || !this.new_name.trim()) return false;
    this.saving = true;
    axios.post('api/items/store', {
      name: this.new_name,
    }).then(response => {
      const itemID = parseIntSafe(response.data.id);
      this.items[itemID] = {
        name: this.new_name,
        filter_name: simplifyString(this.new_name),
        entries: {},
        entry_ids: [],
      };
      this.item_ids.unshift(itemID);
      this.new_name = '';
      this.updateFilter();
      this.selected_item_id = itemID;
    }).catch(error => {
      console.log(error.message);
      alert(getReadableAxiosError(error));
    }).finally(() => {
      this.saving = false;
    });
  },
  
  selectItem(itemID) {
    this.selected_item_id = itemID;
    axios.post(`api/items/${itemID}/update_last_checked_at`);
  },
  
  toggleEditSelectedItemName(toOn) {
    this.new_selected_item_name = this.selectedItem.name;
    this.editing_selected_item_name = toOn;
  },
  
  updateSelectedItemName() {
    if (this.saving || !this.new_selected_item_name.trim()) return false;
    this.saving = true;
    axios.post(`api/items/${this.selected_item_id}/update`, {
      name: this.new_selected_item_name,
    }).then(response => {
      this.items[this.selected_item_id].name = this.new_selected_item_name.trim();
      this.toggleEditSelectedItemName(false);
    }).catch(error => {
      console.log(error.message);
      alert(getReadableAxiosError(error));
    }).finally(() => {
      this.saving = false;
    });
  },
  
  deleteSelectedItem() {
    if (this.saving || !this.selected_item_id) return false;
    let conf = confirm(`Are you sure you want to delete ${this.selectedItem.name}? \nThis cannot be undone.`);
    if (!conf) return false;
    this.saving = true;
    axios.post(`api/items/${this.selected_item_id}/delete`)
      .then(response => {
        const itemID = parseIntSafe(this.selected_item_id);
        this.editEntry(null);
        this.toggleEditSelectedItemName(false);
        this.selected_item_id = null;
        const index = this.item_ids.indexOf(itemID);
        if (index > -1) this.item_ids.splice(index, 1);
        const index2 = this.filtered_item_ids.indexOf(itemID);
        if (index2 > -1) this.filtered_item_ids.splice(index2, 1);
        delete this.items[itemID];
      }).catch(error => {
        console.log(error.message);
        alert(getReadableAxiosError(error));
      }).finally(() => {
        this.saving = false;
      });
  },
  
  editNewEntry() {
    this.new_entry.location = '';
    this.new_entry.is_sale = false;
    this.new_entry.price = null;
    this.new_entry.seen_on = getToday();
    this.new_entry.notes = null;
    this.editEntry('new');
  },
  
  editEntry(entryID) {
    this.selected_entry_id = entryID;
    if (entryID && this.selectedEntry) {
      this.new_entry_location = this.selectedEntry.location;
      this.new_entry_is_sale = !!this.selectedEntry.is_sale;
      this.new_entry_price = this.selectedEntry.price;
      this.new_entry_seen_on = this.selectedEntry.seen_on;
      this.new_entry_notes = this.selectedEntry.notes;
    }
  },
  
  get canSaveEntry() {
    const price = parseFloat(this.new_entry_price);
    return (
      this.new_entry_location
      && typeof this.new_entry_location.trim === 'function'
      && this.new_entry_location.trim().length
      && (
        typeof this.new_entry_price === 'number'
        || (
          this.new_entry_price
          && typeof this.new_entry_price.trim === 'function'
          && this.new_entry_price.trim().length
        )
      )
      && !isNaN(price)
      && this.new_entry_seen_on
      && typeof this.new_entry_seen_on.getMonth === 'function'
    );
  },
  
  seenOnToday() {
    this.datepicker.setDate(new Date(), true);
  },
  
  saveEntry() {
    if (this.saving) return false;
    this.saving = true;
    
    const isNew = this.selected_entry_id === 'new';
    let data = {
      location: this.new_entry_location,
      is_sale: this.new_entry_is_sale,
      price: this.new_entry_price,
      seen_on: dateToYmd(this.new_entry_seen_on),
      notes: this.new_entry_notes,
    };
    if (isNew) data.item_id = this.selected_item_id;
    const url = isNew ?
      'api/entries/store'
      : `api/entries/${this.selected_entry_id}/update`;
    
    axios.post(url, data)
      .then(response => {
        
        let price = parseFloat(this.new_entry_price);
        if (isNaN(price)) price = 0.0;
        
        if (isNew) {
          const entryID = parseIntSafe(response.data.id);
          this.entries[entryID] = {
            id: entryID,
            item_id: parseIntSafe(this.selected_item_id),
            location: this.new_entry_location.trim(),
            is_sale: !!this.new_entry_is_sale,
            price: price,
            seen_on: this.new_entry_seen_on,
            seen_on_diff: diffForHumans(this.new_entry_seen_on),
            notes: this.new_entry_notes,
          };
          this.selectedItem.entry_ids.push(entryID);
        } else {
          this.selectedEntry.location = this.new_entry_location.trim();
          this.selectedEntry.is_sale = !!this.new_entry_is_sale,
          this.selectedEntry.price = price;
          this.selectedEntry.seen_on = this.new_entry_seen_on;
          this.selectedEntry.seen_on_diff = diffForHumans(this.selectedEntry.seen_on);
          this.selectedEntry.notes = this.new_entry_notes;
        }
        
        this.selectedItem.entry_ids.sort((idA, idB) => {
          const priceA = this.entries[idA].price;
          const priceB = this.entries[idB].price;
          if (priceA == priceB) {
            return this.entries[idB].seen_on - this.entries[idA].seen_on;
          }
          return priceA - priceB;
        });
        this.editEntry(null);
      }).catch(error => {
        console.log(error.message);
        alert(getReadableAxiosError(error));
      }).finally(() => {
        this.saving = false;
      });
  },
  
  deleteSelectedEntry() {
    if (this.saving || !this.selected_entry_id) return false;
    let conf = confirm(`Are you sure you want to delete this entry?\nThis cannot be undone.`);
    if (!conf) return false;
    this.saving = true;
    axios.post(`api/entries/${this.selected_entry_id}/delete`)
      .then(response => {
        const entryID = parseIntSafe(this.selected_entry_id);
        const itemID = parseIntSafe(this.selectedEntry.item_id);
        this.editEntry(null);
        const index = this.items[itemID].entry_ids.indexOf(entryID);
        if (index > -1) {
          this.items[itemID].entry_ids.splice(index, 1);
        }
        delete this.entries[entryID];
      }).catch(error => {
        console.log(error.message);
        alert(getReadableAxiosError(error));
      }).finally(() => {
        this.saving = false;
      });
  },
  
});