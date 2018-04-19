<template>
  <div class="field has-addons" :class="{ [`has-addons-${alignment}`]: alignment }">
    <div class="control">
      <a class="button" data-toggle>
        <span class="icon is-small">
          <i class="fa fa-calendar"></i>
        </span>
      </a>
    </div>
    <div class="control flatpickr" data-wrap="true" data-clickOpens="false">
      <input class="input" :class="inputClass" type="text" :placeholder="placeholder" :value="value" data-input>
    </div>
    <div class="control">
      <a class="button" data-clear>
        <span class="icon is-small">
          <i class="fa fa-close"></i>
        </span>
      </a>
    </div>

  </div>
</template>

<script>
import BasicInput from './BasicInput'

export default {
  mixins: [BasicInput],

  props: {
    alignment: String
  }
}
</script>
