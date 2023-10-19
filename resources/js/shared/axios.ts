import axios from 'axios';

export const axiosInstance = axios.create({
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Authorization': 'Bearer ' + localStorage.getItem('token')
    },
})
