import Noty from 'noty'

Noty.overrideDefaults({
  theme: 'bootstrap-v4',
  type: 'success'
})

const displaySuccess = function (successMessage: string) {
  new Noty({
    text: successMessage,
    timeout: 1000
  }).show()
}

const displayWarning = function (warningMessage: string) {
  new Noty({
    text: warningMessage,
    type: 'warning',
    timeout: false
  }).show()
}

const displayError = function (errorMessage: string) {
  new Noty({
    text: errorMessage,
    type: 'error',
    timeout: false
  }).show()
}

export { displaySuccess, displayWarning, displayError }
