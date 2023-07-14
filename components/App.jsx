import { register } from 'swiper/element/bundle';
import { useState, useEffect } from 'react';

import MainCard from './MainCard';
import axios from 'axios';

const App = () => {

	const [data, setData] = useState(null);

	/**
	 * 해당 컴포넌트가 렌더링이 되고난 직후에 동작하는 HOOK
	 */
	useEffect(() => {
		axios.get('/main_board/get_main_board_all.php')
			.then(response => {
				const final_data = make_data_structure(response.data);
				setData(final_data);
				console.log('response:' , response);
				console.log('final_data:' , final_data);
			});
	/**
	 * 빈 배열 : 마운트될 때만 동작
	 */
	}, []);

	return (
		<>
			{data?.map(card => {
				return (
					<MainCard key={'main_card_' + card.id} card={card} />
				)
			})}
		</>
	)
}

export default App;