/**
 * This function si to transform data to http formdata
 * @param {*} data
 * @returns data
 */
export function ToFormData(data) {
  // Do whatever you want to transform the data
  let ret = ''
  for (const it in data) {
    ret += encodeURIComponent(it) + '=' + encodeURIComponent(data[it]) + '&'
  }
  return ret
}
