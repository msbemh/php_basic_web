<script setup>
import { ref, onUpdated } from "vue";

onUpdated(() => {
  // console.log('ListItem onUpdated');
});

const props = defineProps(["item"]);

// console.log('props.item:', props.item);

/**
 * [type]
 * 공지사항 게시글인지 아닌지 확인용
 * normal : 일반 게시글
 * notice : 공지사항 게시글
 */
const type = props.item.type;

function row_click(id) {
  window.location.href = `free_board_detail_read.php?id=${id}`;
}

// console.log('type:', type);
</script>

<template>
  <tr
    :class="[type === 'notice' ? 'free_board_notice_tr' : '']"
    @click="row_click(props.item.id)"
  >
    <td>{{ props.item.id }}</td>
    <td>{{ props.item.title }}</td>
    <td>{{ props.item.views }}</td>
    <td>{{ props.item.create_nick_name }}</td>
    <td>{{ props.item.create_date }}</td>
  </tr>
</template>

