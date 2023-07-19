<script setup>
import ListItem from "./ListItem.vue";
import PageItem from "./PageItem.vue";
import { ref, onMounted, onUpdated, computed } from "vue";

const dataList = ref([]);

const searchValue = ref("");
const searchCategory = ref("title");

const pageInfo = ref({});
const pageList = ref([]);

const isEnableNext = ref(false);
const isEnablePrev = ref(false);

// lifecycle hooks
onMounted(() => {
  get_board_list(1);
});

onUpdated(() => {
  // console.log('App onUpdated');
});

// 검색 버튼 클릭
function search_click() {
  get_board_list(1, {
    category_name: searchCategory.value,
    category_value: searchValue.value,
  });
}

function get_board_list(page, category) {
  /**
   * 카테고리가 존재할 경우, 파라미터로 카테고리 정보를 넘겨준다
   * 카테고리가 없을 경우, 파라미터로 페이지 정보만 넘겨준다
   */
  let url;
  if (category && category.category_name && category.category_value) {
    url = `/free_board/get_free_board.php?page=${page}&category_name=${category.category_name}&category_value=${category.category_value}`;
  } else {
    url = `/free_board/get_free_board.php?page=${page}`;
  }

  $.ajax({
    url: url,
    type: "GET",
    dataType: "json",
    success: function (data) {
      /**
       * 서버에서 게시판 데이터 리스트와
       * 페이징 정보를 가져온다.
       */
      let {
        start_page,
        end_page,
        is_enable_prev,
        is_enable_next,
        next_page,
        prev_page,
        list,
      } = data;

      /**
       * 검색 완료된 이후의 카테고리값 변수에 저장하여
       * 페이징 번호 클릭시, 카테고리 값을 서버로 계속 보내줄 수 있도록 한다
       */
      if (data.category_name && data.category_value) {
        searchCategory.value = data.category_name;
        searchValue.value = data.category_value;
      } else {
        searchCategory.value = 'title';
        searchValue.value = '';
      }

      const current_page = page;

      isEnableNext.value = is_enable_next;
      isEnablePrev.value = is_enable_prev;

      /**
       * 게시판 동적 생성
       */
      dataList.value = list;

      /**
       * 페이징 렌더링
       */
      pageInfo.value = {
        start_page,
        end_page,
        is_enable_prev,
        is_enable_next,
        next_page,
        prev_page,
        current_page,
        search_category: searchCategory.value,
        search_value: searchValue.value
      };
      console.log('pageInfo.value:',pageInfo.value);

      pageList.value = [];
      for(let i=start_page; i<=end_page; i++){
        pageList.value.push(i);
      }

    },
    error: function (xhr, status, error) {
      console.log(error);
    },
  });
}

function move_prev(prev_page) {
  // 링크의 기본 동작을 막음
  // event.preventDefault();
  if (!isEnablePrev.value) return;
  
  get_board_list(prev_page, {
    category_name: searchCategory.value,
    category_value: searchValue.value,
  });
}

function move_next(next_page) {
  // 링크의 기본 동작을 막음
  // event.preventDefault();

  if (!isEnableNext.value) return;

  get_board_list(next_page, {
    category_name: searchCategory.value,
    category_value: searchValue.value,
  });
}

function category_click(category){
  searchCategory.value = category;

  const dropdown_menu_button = document.querySelector('#dropdownMenuButton');
  if (category === 'title') {
      dropdown_menu_button.innerHTML = '제목';
  } else if (category === 'create_user') {
      dropdown_menu_button.innerHTML = '작성자';
  }
}

const categoryNameFn = computed(() => {
  if(searchCategory.value === 'title'){
    return '제목';
  }else if(searchCategory.value === 'create_user'){
    return '작성자';
  }
});

function move_create_free_bard(){
  window.location.href = 'free_board_detail_create.php';
}

</script>

<template>
  <div class="free_board_section layout_padding">
    <div class="container">
      <div class="row" style="padding-left: 15px">
        <div>
          <h1 class="gallery_taital">자유 게시판</h1>
        </div>
      </div>
      <div class="container3">
        <!-- 검색 카테고리 -->
        <div class="dropdown">
          <button
            class="btn btn-secondary dropdown-toggle"
            type="button"
            id="dropdownMenuButton"
            data-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false"
          >
            {{ categoryNameFn }}
          </button>
          <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            <a class="dropdown-item" data-category="title" @click="category_click('title')">제목</a>
            <a class="dropdown-item" data-category="create_user" @click="category_click('create_user')">작성자</a>
          </div>
        </div>
        <input
          type="text"
          class="search_input"
          v-model="searchValue"
          @keyup.enter="search_click"
        />
        <button
          type="button"
          class="btn btn-dark"
          id="search_btn"
          @click="search_click"
        >
          검색
        </button>
        <button type="button" class="btn btn-dark" id="create_btn" @click="move_create_free_bard" >
          글쓰기
        </button>
      </div>
      <div class="">
        <table id="free_board" style="width: 100%">
          <thead>
            <tr>
              <th>번호</th>
              <th>제목</th>
              <th>조회수</th>
              <th>작성자</th>
              <th>작성일</th>
            </tr>
          </thead>
          <tbody>
            <!-- Vue 리스트 아이템 -->
            <ListItem
              v-for="data in dataList"
              :key="data.id"
              :item="data"
            ></ListItem>
          </tbody>
          <tfoot></tfoot>
        </table>
        <!-- 페이징 -->
        <div class="container2">
          <nav class="page_navigation" aria-label="Page navigation example">
            <ul class="pagination">
              <li class="page-item" :disabled="!isEnablePrev">
                <a class="page-link prev" @click.prevent="move_prev(pageInfo.prev_page)" >Previous</a>
              </li>
              <!-- Vue 페이지 아이템 -->
              <PageItem
                v-for="i in pageList"
                :key="i"
                :page="i"
                :item="pageInfo"
                :get_board_list="get_board_list"
              />
              <!-- <li class="page-item"><a class="page-link" href="#">1</a></li> -->
              <!-- <li class="page-item"><a class="page-link" href="#">2</a></li> -->
              <!-- <li class="page-item"><a class="page-link" href="#">3</a></li> -->
              <li class="page-item" :disabled="!isEnableNext" >
                <a class="page-link next" @click.prevent="move_next(pageInfo.next_page)" >Next</a>
              </li>
            </ul>
          </nav>
        </div>
      </div>
    </div>
  </div>
  <!-- <HelloWorld msg="Vite + Vue" /> -->
</template>

<style scoped>
</style>
