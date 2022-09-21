import request from '../utils/request';
import { ToFormData } from '@/utils/toformdata'

export const fetchData = query => {
    return request({
        url: './table.json',
        method: 'get',
        params: query
    });
};
/**
 * 删除通知
 * @param {*} data
 */
export function deleteNotify(data) {
    return request({
        url: '/user/account/deletenotify',
        method: 'post',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded '},
        data,
        transformRequest: [ToFormData]
    })
}
/**
 * 阅读通知
 * @param {*} data
 */
export function readNotify(data) {
    return request({
        url: '/user/account/read-notify',
        method: 'post',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded '},
        data,
        transformRequest: [ToFormData]
    })
}

/**
 * 恢复通知
 * @param {*} data
 */
 export function recoryNotify(data) {
    return request({
        url: '/user/account/recory-notify',
        method: 'post',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded '},
        data,
        transformRequest: [ToFormData]
    })
}

/**
 * 阅读通知细节
 * @param {*} data
 */
export function detailNotify(data) {
    return request({
        url: '/user/account/detail-notify',
        method: 'post',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded '},
        data,
        transformRequest: [ToFormData]
    })
}

export function delOldPeople(data) {
    return request({
        url: '/user/account/del-old-people',
        method: 'post',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded '},
        data,
        transformRequest: [ToFormData]
    })
}
export function delVolunteer(data) {
    return request({
        url: '/user/account/del-volunteer',
        method: 'post',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded '},
        data,
        transformRequest: [ToFormData]
    })
}
export function delEmploy(data) {
    return request({
        url: '/user/account/del-employ',
        method: 'post',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded '},
        data,
        transformRequest: [ToFormData]
    })
}
export function changeUserInfo(data) {
    return request({
        url: '/user/account/change-info',
        method: 'post',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded '},
        data,
        transformRequest: [ToFormData]
    })
}
export function userInfo(data) {
    return request({
        url: '/user/account/user-info',
        method: 'post',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded '},
        data,
        transformRequest: [ToFormData]
    })
}

/**
 * 老人信息录入
 * @param {*} data
 */
 export function oldPeopleManage(data) {
    return request({
        url: '/user/account/old-people-manage',
        method: 'post',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded '},
        data,
        transformRequest: [ToFormData]
    })
}
/**
 * 员工信息信息录入
 * @param {*} data
 */
export function peopleManage(data) {
    return request({
        url: '/user/account/people-manage',
        method: 'post',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded '},
        data,
        transformRequest: [ToFormData]
    })
}

/**
 * 获取老人信息
 * @param {*} data
 */
 export function getOldPeopleInfo() {
    return request({
        url: 'user/account/old-people-info',
        method: 'post',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded '},
    })
}

/**
 * 获取volunteer信息
 * @param {*} data
 */
 export function getVolunteerInfo() {
    return request({
        url: 'user/account/volunteer-info',
        method: 'post',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded '},
    })
}
/**
 * getEmployInfo
 * @param {*} data
 */
 export function getEmployInfo() {
    return request({
        url: 'user/account/employ-info',
        method: 'post',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded '},
    })
}


export function getDataInfo() {
    return request({
        url: '/user/account/notify',
        method: 'post',
    })
}

export function ChangePassword(data) {
    return request({
        url: '/user/account/changepw',
        method: 'post',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded '},
        data,
        transformRequest: [ToFormData]
    })
}

export function login(data) {
    return request({
        url: '/user/account/login',
        method: 'post',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded '},
        data,
        transformRequest: [ToFormData]
    })
}

export function registerForm(data) {
    return request({
        url: '/user/account/register',
        method: 'post',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded '},
        data,
        transformRequest: [ToFormData]
    })
}