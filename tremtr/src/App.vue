<template>
    <div id="reservation" class="trem-reservation" ref="tremReservation" v-if="view !== 4" :style="[canvasLoaded ? {height: envelopeHeight} : {height: hintHeight}] ">
      <div class="hint"  v-if="(rotationHint === true) && (view === 0)" >
        <h3>{{calendarTimeInitData.translation.hintHeader}}</h3>
        <h3>{{calendarTimeInitData.translation.hintText}}<i  class="tremtr-icon-uniF10F"></i></h3>
      </div>
      <transition name="fade" mode="in-out">
        <div class="reservation1" ref="reservationOne" v-show="(rotationHint === false) && (view === 0) && (canvasLoaded === true)">
          <div class="envelope"  :style="[canvasLoaded ? {height: envelopeHeight} : ''] ">
            <h3>{{calendarTimeInitData.translation.header}}</h3>

            <div class="people-form" >
                <div class="form-element">
                  <flat-pickr :config="dateConfig" :placeholder="date" v-model="date" input-class="input"></flat-pickr>
                  <span class="trem-icon tremtr-icon-uniF10A" aria-hidden="true"></span>
                </div>
                <span class="opening-hours" :style="[isDissableDate ? {opacity: 0} : '']">{{calendarTimeInitData.translation.workingHoursOpen}} {{openHoursStart}} - {{openHoursEnd}}</span>
                <label :style="[isDissableDate ? {opacity: 0.5} : '']" >{{calendarTimeInitData.translation.startTime}}</label>
                <div class="form-element" :style="[isDissableDate ? {opacity: 0.5} : '']" >  
                  <flat-pickr :config="startTimeConfig" v-model="timeStart" input-class="input"></flat-pickr>
                  <span class="trem-icon tremtr-icon-uniF10E" aria-hidden="true"></span>
                </div>
                <label :style="[isDissableStartTime ? {opacity: 0.5} : '']" >{{calendarTimeInitData.translation.endTime}}</label>
                <div class="form-element" :style="[isDissableStartTime ? {opacity: 0.5} : '']" >  
                  <flat-pickr :config="finishTimeConfig" v-model="timeEnd" input-class="input"></flat-pickr>
                  <span class="trem-icon tremtr-icon-uniF10C" aria-hidden="true"></span>
                </div>
                <canvas id="cc" class="context-menu-one" width="1000px" height="1000px" ></canvas>
                <a class="c0ffee-button" @click="book">{{calendarTimeInitData.translation.bookTableButton}}</a>
            </div>
          </div>
        </div>
      </transition>
      <transition name="fade" mode="in-out">
        <div class="reservation2" ref="reservationTwo" v-if="view === 1">
            <div class="envelope" ref="reservation2envelope">
                <h3>{{calendarTimeInitData.translation.header}}</h3>
                <div class="info-form">
                  <div class="form-element table">
                    <h4>{{calendarTimeInitData.translation.table}}</h4>
                    <h4>№ {{table}} </h4>
                  </div>
                  <div class="form-element guests" >
                    <h4>{{calendarTimeInitData.translation.for}}</h4>
                    <span> <input 
                      type="number"
                      v-model="persons" 
                      :max="maxPersons"
                      min="1"                     
                      > 
                      <h4>{{calendarTimeInitData.translation.people}}</h4> 
                    </span>
                  </div>
                  <div class="form-element date">
                    <h4>{{calendarTimeInitData.translation.on}}</h4>
                    <h4>{{date}}</h4>
                  </div>
                  <div class="form-element from">
                    <h4>{{calendarTimeInitData.translation.from}}</h4>
                    <h4>{{timeStart}}</h4>
                  </div>
                  <div class="form-element till">
                    <h4>{{calendarTimeInitData.translation.to}}</h4>
                    <h4>{{timeEnd}}</h4>
                  </div>
                  <div class="form-element cafe">
                    <h4>{{calendarTimeInitData.translation.in}}</h4>
                    <h4>{{calendarTimeInitData.translation.cafe}}</h4>
                  </div>
                  <a class="c0ffee-button" id="change" @click="change">{{calendarTimeInitData.translation.changeButton}}</a>
                </div>
                

                <div class="input-form">
                  <div class="input-element name">
                    <input
                      class="name"
                      :placeholder="calendarTimeInitData.translation.name"
                      v-model="name"
                      type="text"
                    />
                    <span 
                      class="trem-icon tremtr-icon-uniF101"
                      v-show="(this.name === '')"

                    ></span>
                    <span 
                      class="trem-icon tremtr-icon-uniF101"
                      v-show="(this.name != '')"
                      style="color: green;"
                    ></span>
                  </div>

                  <div class="input-element email">
                    <input
                      name="email" 
                      v-model="email" 
                      v-validate="'required|email'" 
                      :class="{'input': true, 'is-danger': errors.has('email') }" 
                      type="email" 
                      :placeholder="calendarTimeInitData.translation.email"
                    >
                    <span 
                      class="trem-icon tremtr-icon-uniF10B"
                      v-show="errors.has('email')" 
                      style="color: red;"
                    ></span>
                    <span 
                      class="trem-icon tremtr-icon-uniF10B"
                      v-show="!errors.has('email') && (this.email != '')" 
                      style="color: green;"
                    ></span>
                    <span 
                      class="trem-icon tremtr-icon-uniF10B"
                      v-show="!errors.has('email') && (this.email === '')" 
                      
                    ></span>
                  </div>

                  <div class="input-element phone">
                    <input 
                      name="phone" 
                      v-model="phone" 
                      v-validate="{ rules: { regex: /^[0-9 ()+-]+$/} }" 
                      :class="{'input': true, 'is-danger': errors.has('phone') }" 
                      type="text" 
                      :placeholder="calendarTimeInitData.translation.phone"
                    >

                    <span 
                      class="trem-icon tremtr-icon-uniF105"
                      v-show="errors.has('phone')" 
                      style="color: red;"
                    ></span>
                    <span 
                      class="trem-icon tremtr-icon-uniF105"
                      v-show="!errors.has('phone') && (this.phone != '')" 
                      style="color: green;"
                    ></span>
                    <span 
                      class="trem-icon tremtr-icon-uniF105"
                      v-show="!errors.has('phone') && (this.phone === '')" 
                      
                    ></span>
                  </div>
                  
                  <div class="input-element message">
                    <textarea
                      rows="5"
                      class="message"
                      :placeholder="calendarTimeInitData.translation.message"
                      v-model="message"
                      type="text"
                      wrap="hard"
                    ></textarea>

                    <span 
                      class="trem-icon tremtr-icon-uniF109"
                      v-show="this.message !== ''" 
                      style="color: green;"
                    ></span>
                    <span 
                      class="trem-icon tremtr-icon-uniF109"
                      v-show="this.message === ''" 
                      
                    ></span>
                  </div>
                  <a class="c0ffee-button" id="confirm" @click="confirm">{{calendarTimeInitData.translation.confirmButton}}</a>
                </div>
            </div>
        </div>
      </transition>
      <transition name="fade" mode="in-out">
        <div class="reservation3" v-if="view === 2">
          <div class="confirmation">
            <span class="trem-icon tremtr-icon-uniF104"></span>
            <h3>{{firstName}}</h3>
            <hr>
          </div>
        </div>
      </transition>
    </div>
    
</template>

<script>
import moment from 'moment'
import flatPickr from 'vue-flatpickr-component'

import 'flatpickr/dist/flatpickr.css'

import './assets/font/trem-reservation/css/trem-reservation.css'

import FlatpickrI18n from 'flatpickr/dist/l10n'

export default {
  name: 'reservation',
  components: {
    flatPickr,
    moment
  },
  props: {
    getView: {
      default: 0
    }
  },
  data () {
    return {
      date: '',
      persons: '',
      maxPersons: '',
      timeStart: '',
      timeEnd: '',
      openHoursStart: '',
      openHoursEnd: '',
      table: '',
      name: '',
      message: '',
      email: '',
      phone: '',
      userSurname: '',
      view: 0,
      canvas: '',
      width: 0,
      canvasInitData: '',
      reservations: '',
      disabledTables: [''],
      toast: '',
      calendarTimeInitData: tremtr_data,
      weekDays1: ["monday", "tuesday", "wednesday", "thursday", "friday", "saturday", "sunday"],
      weekDays0: ["sunday", "monday", "tuesday", "wednesday", "thursday", "friday", "saturday"],
      trueTimeFormat: '',
      trueDateFormat: '',
      dbDateFormatForMoment: 'YYYY/MM/DD',
      dbTimeFormatForMoment: 'HH:mm',
      canvasLoaded: false,
      windowWidth: 0,
      windowHeight: 0,
      rotationHint: true,
      mobileWidth: 560,
      minMobileWidth: 450,
      peopleFormHeight: 367,
      hintHeight: 150,
      reservationOne: [],
      reservationTwo: []

    }
  },

  beforeMount: function () {
    this.width = window.innerWidth;
  },

  mounted: function () {

    this.$nextTick(function() {
        window.addEventListener('resize', this.getWindowWidth);
        window.addEventListener('resize', this.getWindowHeight);
    })

    this.getWindowWidth()
    this.getWindowHeight()

    this.initCanvas()

    this.trueTimeFormat = this.pickadateToFlatPickrFormat(this.calendarTimeInitData.time_format)

    this.trueDateFormat = this.pickadateToFlatPickrFormat(this.calendarTimeInitData.date_format)

    document.getElementById('reservation').style.setProperty('--button-color', this.calendarTimeInitData.mainColor)
  }, 

  computed: {

    fillActive: function() { 
      return 'rgba('+ this.calendarTimeInitData.fillActive +',0.4)'
    },

    activeStroke: function() { 
      return 'rgba('+ this.calendarTimeInitData.fillActive +'0)'
    },

     fillHover: function() { 
      return 'rgba('+ this.calendarTimeInitData.fillActive +',0.9)'
    },

     fillBooked: function() { 
      return 'rgba('+ this.calendarTimeInitData.fillBooked +',0.4)'
    },

    bookedFrame: function() { 
      return 'rgba('+ this.calendarTimeInitData.fillBooked +',1)'
    },
    
    envelopeHeight: function() {
      if (this.canvas !== null) {
        if (this.windowWidth > this.minMobileWidth) {
          return (this.canvas.height + this.peopleFormHeight).toString() + 'px'
        } else {
          return (this.hintHeight).toString() + 'px'
        } 
      }
    },

    momentTimeFormat: function() {
      if (this.trueTimeFormat) {
        return this.flatPickrToMomentTimeFormat(this.trueTimeFormat)
      }
    },

    momentDateFormat: function() {
      if (this.trueDateFormat) {
        return this.flatPickrToMomentDateFormat(this.trueDateFormat)
      }
    },

    viewC: function () {
      return this.getView
    },

    isDissableDate: function () {
      return (this.date === '')
    },

    isDissableStartTime: function () {
      return (this.timeStart === '' || this.date === '')
    },

    dayOfWeek: function () {
      moment.locale(this.calendarTimeInitData.translation.calendar)
      return moment(this.date, this.momentDateFormat).locale('en').format('dddd').toLowerCase()
    },

    dateConfig: function () {

      let disabledDates = []
      let disabledDatesFormatted = []
      let disabledDaysOfWeek = []

      for (let closed of this.calendarTimeInitData.schedule_closed){
        if (closed.time === undefined) {
          disabledDates.push(moment(closed.date, this.dbDateFormatForMoment))
        }
      }

      for(let d of disabledDates){
        disabledDatesFormatted.push(d.format(this.momentDateFormat))
      }

      
      let locale = ''
      if (this.calendarTimeInitData.translation.calendar !== 'en') {
        locale = FlatpickrI18n[this.calendarTimeInitData.translation.calendar]
        locale.firstDayOfWeek  = this.calendarTimeInitData.week_start
      } else {
         locale = {firstDayOfWeek: this.calendarTimeInitData.week_start}
      }
      

      let timeFinish = ''
      let timeStart = ''

      for (let open of this.calendarTimeInitData.schedule_open){
        let keyNames = Object.keys(open.weekdays);

        if (this.calendarTimeInitData.week_start === '0') {

          for (let i of this.weekDays0) {
            if (keyNames.includes(i)) {
              disabledDaysOfWeek.push(this.weekDays0.indexOf(i));
            }
          }
        }

        if (this.calendarTimeInitData.week_start === '1') {

          for (let i of this.weekDays1) {
            if (keyNames.includes(i)) {
              disabledDaysOfWeek.push(this.weekDays0.indexOf(i));
            }
          }
        }
      }


      let momentDateFormat =  this.momentDateFormat;

      return {
        defaultDate: null,
        disableMobile: true,
        dateFormat: this.trueDateFormat,
        locale: locale,
        minDate: "today",
        maxDate: new Date().fp_incr(Number(this.calendarTimeInitData.early_reservations)),
        disable: [
            function(date) {
              return (!(disabledDaysOfWeek.includes(date.getDay())) || disabledDatesFormatted.includes(moment(date).format(momentDateFormat)));
            }
        ]
      }
    },

    startTimeConfig: function () {

      if (this.isDissableDate) {
        this.timeStart = ''
      } 

      if (this.date != '') {

        this.renewDisabledTables ()    //also for endTimeConfig
        this.disableTable ()  //

        let timeFinish = ''
        let timeStart = ''

        for (let open of this.calendarTimeInitData.schedule_open){
          let keyNames = Object.keys(open.weekdays);
          for (let i of keyNames) {
            if (i === this.dayOfWeek) {
              timeFinish = open.time.end;
              timeStart = open.time.start;
            }
          }
        }

        for (let open of this.calendarTimeInitData.schedule_closed){
          moment.locale(this.calendarTimeInitData.translation.calendar)
          if (open.date === moment(this.date, this.momentDateFormat).locale('en').format(this.dbDateFormatForMoment)) { 
            timeStart = open.time.start
            timeFinish = open.time.end 
          }
        }

        this.openHoursStart = timeStart;
        this.openHoursEnd = timeFinish;

        moment.locale(this.calendarTimeInitData.translation.calendar)
        if (moment().date() === moment(this.date, this.momentDateFormat).locale('en').date()) {
          let time = moment(timeStart, this.dbTimeFormatForMoment)

          if (Number(time.hours()) <= Number(moment().hours())) {
            time = moment()
            let addMinutes = Number(this.calendarTimeInitData.late_reservations) 
            time.add(addMinutes, 'm')
            let roundTime = Number(time.minutes()) % Number(this.calendarTimeInitData.time_interval) 
            time.add(-roundTime, 'm')
            
            if (Number(moment(timeFinish, this.dbTimeFormatForMoment).diff(time)) < 0) {
              this.date = ''
            }
            timeStart = time.format(this.momentTimeFormat)
          }
        }

        let time24hr = false;
        if (this.calendarTimeInitData.time_format.match(/A/g) === null){
          time24hr = true;
        }


        if (this.timeStart === '') {
          this.timeStart = timeStart
        } 

        
        return {
          time_24hr: time24hr,
          enableTime: true,
          noCalendar: true,
          dateFormat: this.trueTimeFormat, 
          minuteIncrement: this.calendarTimeInitData.time_interval, 
          minDate: timeStart, 
          maxDate: timeFinish
        }

      } else {

        let time24hr = false;
        if (this.calendarTimeInitData.time_format.match(/A/g) === null){
          time24hr = true;
        }

        return {
          time_24hr: time24hr,
          enableTime: true,
          noCalendar:true,
          minuteIncrement: this.calendarTimeInitData.time_interval
        }

      } 
    },

    finishTimeConfig: function () {

      let time = moment(this.timeStart, this.momentTimeFormat) 
      time.add(Number(this.calendarTimeInitData.time_interval), 'm')

      let timeF = '';

      for (let open of this.calendarTimeInitData.schedule_open){
        let keyNames = Object.keys(open.weekdays);
        for (let i of keyNames) {
          if (i === this.dayOfWeek) {
           timeF = moment(open.time.end, this.dbTimeFormatForMoment).format(this.momentTimeFormat);
          }
        }
      }

      for (let open of this.calendarTimeInitData.schedule_closed){
        moment.locale(this.calendarTimeInitData.translation.calendar)
        if (open.date === moment(this.date, this.momentDateFormat).locale('en').format(this.dbDateFormatForMoment)) { 
          timeF = moment(open.time.end, this.dbTimeFormatForMoment).format(this.momentTimeFormat);
        }
      }

      let time24hr = false;
        if (this.calendarTimeInitData.time_format.match(/A/g) === null){
          time24hr = true;
      }

      if (this.isDissableStartTime) {
        this.timeEnd = ''
      } else {
        if (this.timeEnd === '' || (moment(this.timeEnd, this.momentTimeFormat).diff(moment(this.timeStart, this.momentTimeFormat)) < 0) ) {
          this.timeEnd = moment(this.timeStart, this.momentTimeFormat).add(Number(60), 'm').format(this.momentTimeFormat)
        } 
        if ((moment(this.timeEnd, this.momentTimeFormat).diff(moment(this.openHoursStart, this.momentTimeFormat)) < 0) || (moment(this.timeEnd, this.momentTimeFormat).diff(moment(this.openHoursEnd, this.momentTimeFormat)) > 0) ) {
          this.timeEnd = moment(this.openHoursEnd, this.momentTimeFormat).format(this.momentTimeFormat)
        } 
      }

      return {
        enableTime:true, 
        minuteIncrement: this.calendarTimeInitData.time_interval, 
        dateFormat: this.trueTimeFormat, 
        noCalendar:true, 
        minDate: time.format(this.momentTimeFormat), 
        maxDate: timeF,
        time_24hr: time24hr
      }
    },

    selectable: function () {
      if (this.canvas !== ''){
        if ((this.date !== '') && (this.timeEnd !== '') && (this.timeStart !== '')) {
          this.makeTablesSelectable()
        }
      }
    },

    firstName: function () {
      if (this.name !== ''){
        if (this.name.indexOf(' ') >= 0) {

          return this.name.substr(0,this.name.indexOf(' ')) + ', ' + this.calendarTimeInitData.translation.thanksAtTheEnd
        } else {

          return this.name + ', ' + this.calendarTimeInitData.translation.thanksAtTheEnd
        }
      }
    }

  },

  watch: {
    // whenever question changes, this function will run
    date: function (newDate) {
      this.makeTablesSelectable()
    },

    timeStart: function (newTimeStart) {
      this.makeTablesSelectable()
    },

    timeEnd: function (newTimeEnd) {
      this.makeTablesSelectable()
    },

    windowHeight: function (newWindowHeight) {
      if ( (this.windowWidth > this.minMobileWidth)) {
        this.rotationHint = false
      } else {
          this.rotationHint = true
      }
    },

    windowWidth: function (newWindowWidth) {
      if ( (this.windowWidth > this.minMobileWidth)) {
        this.rotationHint = false
      } else {
          this.rotationHint = true
      }
    }
  },

  methods: {

    getWindowWidth(event) {
      this.windowWidth = document.getElementById("reservation").parentNode.parentElement.clientWidth;
    },

    getWindowHeight(event) {
      this.windowHeight = document.documentElement.clientHeight;
    },

    flatPickrToMomentTimeFormat (str) {
      let formattedStr = str;
      formattedStr = formattedStr.replace('K' , 'A')
      formattedStr = formattedStr.replace('H' , 'HH')
      formattedStr = formattedStr.replace('i' , 'mm')
      return (formattedStr)
    },

    flatPickrToMomentDateFormat (str) {
      let formattedStr = str;

      if (formattedStr.match(/d/g) !== null) {
        formattedStr = formattedStr.replace('d' , 'DD')

      } else if (formattedStr.match(/D/g) !== null) {
        formattedStr = formattedStr.replace('D' , 'ddd')

      } else if (formattedStr.match(/l/g) !== null) {
        formattedStr = formattedStr.replace('l' , 'dddd')

      } else if (formattedStr.match(/j/g) !== null) {
        formattedStr = formattedStr.replace('j' , 'D')
      } 


      if (formattedStr.match(/m/g) !== null) {
        formattedStr = formattedStr.replace('m' , 'MM')

      } else if (formattedStr.match(/n/g) !== null) {
        formattedStr = formattedStr.replace('n' , 'M')

      } else if (formattedStr.match(/M/g) !== null) {
        formattedStr = formattedStr.replace('M' , 'MMM')

      } else if (formattedStr.match(/F/g) !== null) {
        formattedStr = formattedStr.replace('F' , 'MMMM')
      } 

      formattedStr = formattedStr.replace('Y' , 'YYYY')
      return (formattedStr)
    },

    pickadateToFlatPickrFormat (str) {
      let formattedStr = str;
      if (formattedStr.match(/dddd/g) !== null) {
        formattedStr = formattedStr.replace('dddd' , 'l')

      } else if (formattedStr.match(/ddd/g) !== null) {
        formattedStr = formattedStr.replace('ddd' , 'D')

      } else if (formattedStr.match(/dd/g) !== null) {
        formattedStr = formattedStr.replace('dd' , 'd')

      } else if (formattedStr.match(/d/g) !== null) {
        formattedStr = formattedStr.replace('d' , 'j')
      } 

      if (formattedStr.match(/mmmm/g) !== null) {
        formattedStr = formattedStr.replace('mmmm' , 'F')

      } else if (formattedStr.match(/mmm/g) !== null) {
        formattedStr = formattedStr.replace('mmm' , 'M')

      } else if (formattedStr.match(/mm/g) !== null) {
        formattedStr = formattedStr.replace('mm' , 'm')

      } else if (formattedStr.match(/m/g) !== null) {
        formattedStr = formattedStr.replace('m' , 'n')
      }

      formattedStr = formattedStr.replace('yyyy' , 'Y')

      formattedStr = formattedStr.replace('HH' , 'H')
      formattedStr = formattedStr.replace('A' , 'K')
      return (formattedStr)
    },

    flatPickrToPickadateFormat (str) {
      let formattedStr = str;

      if (formattedStr.match(/l/g) !== null) {
        formattedStr = formattedStr.replace('l' , 'dddd')

      } else if (formattedStr.match(/D/g) !== null) {
        formattedStr = formattedStr.replace('D' , 'ddd')

      } else if (formattedStr.match(/d/g) !== null) {
        formattedStr = formattedStr.replace('d' , 'dd')

      } else if (formattedStr.match(/j/g) !== null) {
        formattedStr = formattedStr.replace('j' , 'd')
      } 

      if (formattedStr.match(/F/g) !== null) {
        formattedStr = formattedStr.replace('F' , 'mmmm')

      } else if (formattedStr.match(/M/g) !== null) {
        formattedStr = formattedStr.replace('M' , 'mmm')

      } else if (formattedStr.match(/m/g) !== null) {
        formattedStr = formattedStr.replace('m' , 'mm')

      } else if (formattedStr.match(/n/g) !== null) {
        formattedStr = formattedStr.replace('n' , 'm')
      }

      formattedStr = formattedStr.replace('Y' , 'yyyy')

      formattedStr = formattedStr.replace('H' , 'HH')
      formattedStr = formattedStr.replace('K' , 'A')
      return (formattedStr)
    },

    book () {
      if ((this.date != '') && (this.timeStart != '') && (this.timeFinish != '') && (this.table != '')) {


        let elementRes = document.getElementById('reservation')
        let offsetHeight = elementRes.offsetHeight

        this.$refs.reservationOne.style.display = 'none';
        this.view = 1

        setTimeout(function () {
          this.$refs.tremReservation.style.height = (this.$refs.reservation2envelope.clientHeight + 30).toString() + 'px'
        }.bind(this), 500)


        if (this.windowHeight*1.5 < offsetHeight) {
          //var e = document.getElementsByClassName("cap")  + e[0].offsetHeight
          window.scrollTo(0, elementRes.offsetTop)
        }
        
      } else {
        this.$toasted.show(this.calendarTimeInitData.translation.bookTableButtonWarning, { 
          theme: "primary", 
          position: "top-center", 
          duration : 3000,
          className: 'toast',
          containerClass: 'toast-container'
        });
      }
    },

    confirm () {
      if ((this.name !== '') && (this.mail !== '') && (this.phone !== '') && (!this.errors.has('email')) && (!this.errors.has('phone'))) {
        moment.locale(this.calendarTimeInitData.translation.calendar)
        this.$http.post(this.calendarTimeInitData.url, {
          'action': 'tremtr_reservation',
          'nonce': this.calendarTimeInitData.nonce,
          'tremtr_reservation_date': moment(this.date, this.momentDateFormat).format(this.dbDateFormatForMoment),
          'tremtr_reservation_time_begin': moment(this.timeStart, this.momentTimeFormat).format(this.dbTimeFormatForMoment),
          'tremtr_reservation_time_end': moment(this.timeEnd, this.momentTimeFormat).format(this.dbTimeFormatForMoment),
          'tremtr_reservation_table': this.table,
          'tremtr_reservation_name': this.name,
          'tremtr_reservation_persons': this.persons,
          'tremtr_reservation_email': this.email,
          'tremtr_reservation_phone': this.phone,
          'tremtr_reservation_message': this.message
        },
        {
          emulateJSON: true
        }).then(response => {


          if (JSON.parse(response.bodyText).success === true) {

            this.view = 2

            setTimeout(function () {
              this.$refs.tremReservation.style.height =  '300px'
            }.bind(this), 500)

            setTimeout(function () {
              this.hideReservation()
            }.bind(this), 3000)

          } else {

            this.$toasted.show(this.calendarTimeInitData.translation.rejected, { 
              theme: "primary", 
              position: "top-center", 
              duration : 7000,
              className: 'toast',
              containerClass: 'toast-container'
            });

            this.$http.get(this.calendarTimeInitData.endpoint_reservation).then(response => {

              // get body data 
              this.reservations = response.body
              for (let reservation of this.reservations){
                reservation.tremtr_reservation_date = moment(reservation.tremtr_reservation_date, this.dbDateFormatForMoment).format(this.momentDateFormat)
                reservation.tremtr_reservation_time_begin = moment(reservation.tremtr_reservation_time_begin, this.dbTimeFormatForMoment).format(this.momentTimeFormat)
                reservation.tremtr_reservation_time_end = moment(reservation.tremtr_reservation_time_end, this.dbTimeFormatForMoment).format(this.momentTimeFormat)
              }

            }, response => {

              // error callback 
            });
          }
          

        }, response => {
          // error callback
        });

        
      } else {
        this.$toasted.show(this.calendarTimeInitData.translation.confirmButtonWarning, { 
          theme: "primary", 
          position: "top-center", 
          duration : 3000,
          className: 'toast',
          containerClass: 'toast-container'
        });
      }
    },
    change () {

      this.$refs.reservationTwo.style.display = 'none';
      this.view = 0

      setTimeout(function () {
        this.$refs.tremReservation.style.height = this.envelopeHeight
      }.bind(this), 500)

    },
    changeDate () {
      this.date = ''
    },
    hideReservation () {
      this.view = 4
    },

    switchReservation () {
      if (this.view < 4) {
        this.view = 4
        this.activeButton.active = false
      } else {
        this.view = 0
        this.activeButton.active = true
      }
    },

    initCanvasObject (obj){

      obj.lockMovementX = true; 
      obj.lockMovementY = true; 
      obj.lockScalingX = true;
      obj.lockScalingY = true;
      obj.lockUniScaling = true;
      obj.lockRotation = true; 
      obj.hasControls = false;
      obj.hoverCursor = 'pointer';
      obj.hasBorders = false;
      obj.opacity = 0;
      obj.selectable = false;
      obj.evented = false;
    },

    initCanvas () {

      let fillActive = this.fillActive
      let fillHover = this.fillHover
      let fillBooked = this.fillBooked


      this.$http.get(this.calendarTimeInitData.endpoint_cafe).then(response => {

        // get body data 
        this.canvasInitData = response.body[0].tremtr_content

        this.canvas = new fabric.Canvas('cc', { selection: false })

        this.canvas.loadFromJSON(this.canvasInitData);

        let parsedInfo =  JSON.parse(this.canvasInitData);


        if (parsedInfo.backgroundImage == null ){

          this.canvas.setHeight(300)
          this.canvas.setWidth(200)

          this.$toasted.show("ADD IMAGE!", { 
            theme: "bubble", 
            position: "top-center", 
            duration : 30000,
            className: 'toast',
            containerClass: 'toast-container'
          });

          this.canvas.renderAll()

        } else {

          let width = this.windowWidth;
          if (width > parsedInfo.backgroundImage.width) {
            width = parsedInfo.backgroundImage.width;
          }

          if (width < this.minMobileWidth) {
            width = this.windowHeight;
          }

          let scale = (width*0.85)/(parsedInfo.backgroundImage.width*parsedInfo.backgroundImage.scaleX);
          this.canvas.setZoom(scale)
          this.canvas.renderAll()

          this.canvas.setHeight(parsedInfo.backgroundImage.height * parsedInfo.backgroundImage.scaleY * scale)
          this.canvas.setWidth(parsedInfo.backgroundImage.width * parsedInfo.backgroundImage.scaleX * scale)
          this.canvas.renderAll()
        }

        //add id to canvas objects and lock objects
        
        let objCounter = 1;
        let counter = 0;
        let canvasObjectId = 0;

        for(let i = 0; i < this.canvas.getObjects().length; i+=3){
          
          this.canvas.item(i).id = this.canvas.item(i + 1).text;
          this.canvas.item(i + 1).id = this.canvas.item(i + 1).text
          this.canvas.item(i + 2).id = this.canvas.item(i + 1).text

          this.initCanvasObject (this.canvas.item(i))
          this.initCanvasObject (this.canvas.item(i + 1))
          this.initCanvasObject (this.canvas.item(i + 2))
          
          let tablePeople = [this.canvas.item(i+1).text, this.canvas.item(i+2).text]
          this.canvas.item(i).name = tablePeople;

          this.canvas.item(i).set({
            fill: fillActive,
            strokeWidth : 0,
            opacity: 1
          });

          this.canvas.item(i+1).set({
            opacity: 1,
            left: this.canvas.item(i).left + 10,
            top: this.canvas.item(i).top + 10,
            fontSize: 20,
            fontWeight: 'bold'
          });

          this.canvas.item(i+2).set({
            text: ''
          });

          this.canvas.renderAll()
        }

        var _self = this;
        let called = false //for toasts

        this.canvas.on('mouse:over', function(e) {


          if (e.target != null) {
            if ((e.target.fill === fillActive) && (e.target.type === 'rect')) {
              e.target.set({
                 fill: fillHover
              });
            }
            e.target.canvas.renderAll();
          }
        });


        this.canvas.on('mouse:out', function(e) {
          if (e.target != null) {
            if ((e.target.fill === fillHover)  && (e.target.type === 'rect')) {
              if (_self.canvas.item(_self.canvas.getObjects().indexOf(e.target)+2).opacity != 1) {
                e.target.set({
                  fill: fillActive
                });

              }
            }
            e.target.canvas.renderAll();
          }
        });
        
        _self.canvas.observe('mouse:down',function(e) {

          if (((_self.date === '') || (_self.timeEnd === '') || (_self.timeStart === '')) && !(called)) {

            _self.$toasted.show(_self.calendarTimeInitData.translation.canvasClickWarning, { 
              theme: "primary", 
              position: "top-center", 
              duration : 2000,
              className: 'toast',
              containerClass: 'toast-container'
            });

            called = !called
            setTimeout(function(){
              called = !called
            }, 5000)
          }

          if (e.target != null) {

            for(let i = 0; i < _self.canvas.getObjects().length; i++){
              if (_self.canvas.item(i).fill === fillHover){
                _self.canvas.item(i).set({
                  fill: fillActive
                });
                _self.canvas.item(i+2).set({
                  opacity: 0
                });
              }
            }

            if (e.target.type === 'rect') {
              let index = _self.canvas.getObjects().indexOf(e.target)

              e.target.set({
                fill: fillHover
              });

              _self.canvas.item(_self.canvas.getObjects().indexOf(e.target)+2).set({
                opacity: 1
              });
              
              _self.table = e.target.name[0];
              _self.persons = e.target.name[1];
              _self.maxPersons = e.target.name[1];
            }
            
            if (e.target.type === 'text') {
              let index = _self.canvas.getObjects().indexOf(e.target)

              _self.canvas.item(index-1).set({
                fill: fillHover
              });

              _self.table = _self.canvas.item(index-1).name[0];
              _self.persons = _self.canvas.item(index-1).name[1];
              _self.maxPersons = _self.canvas.item(index-1).name[1];
            }
            
            _self.canvas.renderAll()
          }
        });

        if (this.table !== '') {
          this.selectTable ()
        }

        this.canvasLoaded = true
      }, response => {
        // error callback 
      });

      this.$http.get(this.calendarTimeInitData.endpoint_reservation).then(response => {

        // get body data 
        this.reservations = response.body
        for (let reservation of this.reservations){
          reservation.tremtr_reservation_date = moment(reservation.tremtr_reservation_date, this.dbDateFormatForMoment).format(this.momentDateFormat)
          reservation.tremtr_reservation_time_begin = moment(reservation.tremtr_reservation_time_begin, this.dbTimeFormatForMoment).format(this.momentTimeFormat)
          reservation.tremtr_reservation_time_end = moment(reservation.tremtr_reservation_time_end, this.dbTimeFormatForMoment).format(this.momentTimeFormat)
        }

      }, response => {

        // error callback 
      });
    },

    renewDisabledTables () {

      this.disabledTables = []

      if (this.timeStart != '') {

        let todaysReservations = this.reservations.filter(reservation => reservation.tremtr_reservation_date.toLowerCase() === this.date.toLowerCase());

        let selectedTimeStart = moment(this.timeStart, this.momentTimeFormat)

        for (let todaysReservation of todaysReservations){
          let timeBegin = moment(todaysReservation.tremtr_reservation_time_begin, this.momentTimeFormat)
          let timeEnd = moment(todaysReservation.tremtr_reservation_time_end, this.momentTimeFormat)
          if ((selectedTimeStart.diff(timeBegin) >= 0) && (selectedTimeStart.diff(timeEnd) <= 0)) {
            this.disabledTables.push(todaysReservation.tremtr_reservation_table)        
          }
        }

        if (this.timeEnd != '') {
        
          let selectedTimeEnd = moment(this.timeEnd, this.momentTimeFormat)

          for (let todaysReservation of todaysReservations){
            let timeBegin = moment(todaysReservation.tremtr_reservation_time_begin, this.momentTimeFormat)
            let timeEnd = moment(todaysReservation.tremtr_reservation_time_end, this.momentTimeFormat)
            if ((selectedTimeEnd.diff(timeBegin) >= 0) && (selectedTimeEnd.diff(timeEnd) <= 0) || ((selectedTimeStart.diff(timeBegin) <= 0) && (selectedTimeEnd.diff(timeEnd) >= 0)) ) {
              this.disabledTables.push(todaysReservation.tremtr_reservation_table)        
            }
          }
        }
      }
    },

    disableTable () {
      
      for(let i = 0; i < this.canvas.getObjects().length; i= i+3){
        if (this.disabledTables.includes(this.canvas.item(i).name[0])) {
          this.canvas.item(i).set({
            fill: this.fillBooked,
            evented: false
          });

          if (this.canvas.item(i).name[0] === this.table) {
            this.canvas.item(i).set({
              fill: this.fillBooked,
              evented: false
            });
            this.canvas.item(i+2).set({
              opacity: 0
            });
            this.table = ''
          }
        }

        if (!(this.disabledTables.includes(this.canvas.item(i).name[0]))) {
          if (this.canvas.item(i).fill === this.fillBooked) {
            this.canvas.item(i).set({
              fill: this.fillActive,
              evented: true
            });
          }
        }

        
      }
      this.canvas.renderAll()
    },

    selectTable () {
      
      for(let i = 0; i < this.canvas.getObjects().length; i= i+3){
        if (this.canvas.item(i).name[0] === this.table) {
          this.canvas.item(i).set({
            fill: this.fillHover
          });
          this.canvas.item(i+2).set({
            opacity: 1
          });
        }
      }
      this.canvas.renderAll()
    },

    makeTablesSelectable () {

        if (this.canvas !== ''){
            if ((this.date !== '') && (this.timeEnd !== '') && (this.timeStart !== '')) {
              for(let i = 0; i < this.canvas.getObjects().length; i= i+3){

                if (!(this.disabledTables.includes(this.canvas.item(i).name[0]))) { 

                  this.canvas.item(i).set({
                    evented: true
                  });
                }

              }
              this.canvas.renderAll()
            } else {
              for(let i = 0; i < this.canvas.getObjects().length; i= i+3){

                if (!(this.disabledTables.includes(this.canvas.item(i).name[0]))) {

                  this.canvas.item(i).set({
                    evented: false
                  });
                  this.canvas.item(i+2).set({
                    opacity: 0
                  });
                }
                if (this.canvas.item(i).name[0] === this.table) {
                  this.canvas.item(i).set({
                    fill: this.fillActive
                  });
                  this.table = ''
                }
              }
              this.canvas.renderAll()
            }
  
        }
      
     
    }
  },

  beforeDestroy() {
    window.removeEventListener('resize', this.getWindowWidth);
    window.removeEventListener('resize', this.getWindowHeight);
  }
}
</script>

<style lang="scss"> 

  

</style>


<style scoped>

  
</style>

