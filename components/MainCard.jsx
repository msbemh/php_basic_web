// import function to register Swiper custom elements
import { register } from 'swiper/element/bundle';

// register Swiper custom elements
register();

import axios from 'axios';

import { useState } from 'react';

const MainCard = ({ card, children }) => {

    /**
     * 예전에는 data-id 이런식으로 html에 데이터를 넣었는데
     * 이젠 컴포넌트 아래 변수에서 관리가 되니 너무 좋구용^^
     */
    const id = card.id;
    const create_user = card.create_user;

    const [isHeartExist, setIsHeartExist] = useState(!is_empty(card.is_heart_click));
    const [totalCnt, setTotalCnt] = useState(card.heart_cnt);
    const [isFollowed, setIsFollowed] = useState(!is_empty(card.is_following));

    const onClickHeart = (e) => {
        axios({
            method: 'post',
            url: '/main_board/post_heart.php',
            data: {
                main_board_id: id
            },
            responseType: 'json'
        }).then(response => {
            console.log('response:', response);
            const data = response.data;
            setIsHeartExist(data.is_heart_exist);
            setTotalCnt(data.total_cnt);
        })
        .catch(function (error) {
            console.log(error);
        });
    };

    const onClickChat = (e) => {
        window.location.href = `/main_board/main_board_detail_read.php?main_board_id=${id}`;
    };

    const onClickFollow = (e) => {
        axios({
            method: 'post',
            url: '/main_board/post_follow.php',
            data: {
                opponent_email: create_user
            },
            responseType: 'json'
        }).then(response => {
            console.log('response:', response);
            const data = response.data;
            const is_followed = data.is_followed;
            setIsFollowed(is_followed);
        })
        .catch(function (error) {
            console.log(error);
        });
    };

    return (
        <>
            {/* 카드 */}
            <div className="card_item" style={{ maxWidth: '720px' }}>
                {/* 헤더 */}
                <div className="card_header relative_box">
                    {/* 프로필 이미지 */}
                    <div className="WriterInfo pointer left relative_viertical margin_left_30">
                        <div className="user_profile_header_box">
                            <img className="pointer" src={!is_empty(card.profile_img) ? card.profile_img : default_profile_img} /></div>
                    </div>
                    {/* 닉네임 */}
                    <div className="left relative_box relative_viertical margin_left">{card.nick_name}</div>
                    {/* 생성일 */}
                    <div className="left relative_box relative_viertical margin_left">{card.create_date}</div>

                    {/**  
                      * 팔로잉, 팔로우 
                      * 내가 만든 카드는 팔로우, 팔로잉 보이지 않게 한다
                      */}
                    {
                        session_email === create_user ? null : <div onClick={onClickFollow} className="left relative_box relative_viertical margin_left pointer follow">{isFollowed ? '팔로잉' : '팔로우'}</div>
                    }
                    {/* ... 이미지 */}
                    <div className="right relative_box relative_viertical pointer more_img margin_right_20"></div>
                </div>
                {/* 바디 */}
                <div className="card_body relative_box ">
                    <swiper-container
                        style={{ height: '100%', width: '100%' }}
                        navigation="true"
                        pagination="true" >
                        {/* 컨텐츠 파일 리스트 */}
                        {card.file_list?.map(file => {
                            return (
                                <swiper-slide 
                                    key={file.path}>
                                    <img 
                                        src={file.path} 
                                        style={{ height: '100%', width: '100%', objectFit: 'cover' }}/>
                                </swiper-slide>
                            )
                        })}
                    </swiper-container>
                </div>
                {/* 푸터 */}
                <div className="card_footer relative_box">
                    {/* 좋아요 */}
                    <div onClick={onClickHeart} className="heart_container pointer left relative_box">
                        <img className="heart_img" src={isHeartExist ? "/images/free-icon-hearts-red.png" : "/images/free-icon-heart-empty.png"} />
                        <div className="clear heart_cnt" >좋아요 {totalCnt}개</div>
                    </div>
                    {/* 댓글 view로 이동 */}
                    <div onClick={onClickChat} className="heart_container pointer left margin_left_30">
                        <img src="/images/free-icon-chat-bubble.png" className="chat_img"  />
                    </div>
                </div>
            </div>
        </>
    );
}

export default MainCard;
