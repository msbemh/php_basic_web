<script setup>
import { ref, onUpdated, onBeforeUpdate } from "vue";

onBeforeUpdate(() => {
  // console.log('PageItem onUpdated');
  // console.log('page:', page);
  // console.log('current_page:', current_page);
  // console.log('props.item.current_page:', props.item.current_page);
  current_page = props.item.current_page;
  search_category = props.item.search_category;
  search_value = props.item.search_value;

});

const props = defineProps(["item", "page", "get_board_list"]);

// console.log("props.item:", props.item);
// console.log("props.page:", props.page);

const page = props.page;

const start_page = props.item.start_page;
const end_page = props.item.end_page;
const is_enable_prev = props.item.is_enable_prev;
const is_enable_next = props.item.is_enable_next;
const next_page = props.item.next_page;
const prev_page = props.item.prev_page;
var current_page = props.item.current_page;
const get_board_list = props.get_board_list;
var search_category = props.item.search_category;
var search_value = props.item.search_value;

// console.log("get_board_list:", get_board_list);

function page_click() {
  get_board_list(page, {
    category_name: search_category,
    category_value: search_value,
  });
}


// const props2 = defineProps(['item']);

// console.log('props2.item:', props2.item);
</script>

<template>
  <li class="page-item" :class="[page === current_page ? 'active' : '']">
    <a class="page-link" @click="page_click">{{ page }}</a>
  </li>
</template>

