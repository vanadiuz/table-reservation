<template>
    <div id="reservation" class="reservation" v-if="view !== 4" >
      <div class="hint"  v-if="(rotationHint === true) && (view === 0)" >
        <h2>{{calendarTimeInitData.translation.hintHeader}}</h2>
        <h2>{{calendarTimeInitData.translation.hintText}}<i  class="tremtr-icon-uniF10F"></i></h2>
      </div>
    <transition name="fade" mode="in-out">
      <div class="reservation1" ref="reservationOne" v-show="(rotationHint === false) && (view === 0) && (canvasLoaded === true)">
        <div class="envelope"  :style="[canvasLoaded ? {height: envelopeHeight} : ''] ">
          <div class="exit">
            <icon name="times" scale="2"></icon>
          </div>
          <div class="header-center">
              <h2>{{calendarTimeInitData.translation.header}}</h2>
          </div>
          <div class="people-form" >
              <div class="form-element">
                <flat-pickr :config="dateConfig" :placeholder="date" v-model="date" ></flat-pickr>
                  <span class="book-icon">
                      <i class="tremtr-icon-uniF10A" aria-hidden="true"  style="color:#1e1914"></i>
                  </span>
              </div>
              <p class="opening-hours" v-if="isDissableDate === false">{{calendarTimeInitData.translation.workingHoursOpen}} 564 {{openHoursStart}} - {{openHoursEnd}}</p>
              <p :style="[isDissableDate ? {opacity: 0.5} : '']" >{{calendarTimeInitData.translation.startTime}}</p>
              <div class="form-element" :style="[isDissableDate ? {opacity: 0.5} : '']" >  
                <flat-pickr :config="startTimeConfig" v-model="timeStart" ></flat-pickr>
                  <span class="book-icon">
                    <i class="tremtr-icon-uniF10C" aria-hidden="true"  style="color:#1e1914"></i>
                  </span>
              </div>
              <p :style="[isDissableStartTime ? {opacity: 0.5} : '']" >{{calendarTimeInitData.translation.endTime}}</p>
              <div class="form-element" :style="[isDissableStartTime ? {opacity: 0.5} : '']" >  
                <flat-pickr :config="finishTimeConfig" v-model="timeEnd" ></flat-pickr>
                  <span class="book-icon">
                    <i class="tremtr-icon-uniF10E" aria-hidden="true"  style="color:#1e1914"></i>
                  </span>
              </div>
              <canvas id="cc" class="context-menu-one" width="1000px" height="1000px" ></canvas>
              <div class="c0ffee-button">
                <a @click="book"><h4>{{calendarTimeInitData.translation.bookTableButton}}</h4></a>
              </div>
              
          </div>
        </div>
      </div>
    </transition>
    <transition name="fade" mode="in-out">
      <div class="reservation2" ref="reservationTwo" v-if="view === 1">
          <div class="envelope">
              <div class="header-center">
                  <h2>{{calendarTimeInitData.translation.header}}</h2>
              </div>

              <div class="grid-container">

                <div class="info-form">
                  <div class="form-element table">
                    <p>{{calendarTimeInitData.translation.table}}</p>
                    <h4>№ {{table}} </h4>
                  </div>
                  <div class="form-element guests" >
                    <p>{{calendarTimeInitData.translation.for}}</p>
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
                    <p>{{calendarTimeInitData.translation.on}}</p>
                    <h4>{{date}}</h4>
                  </div>
                  <div class="form-element from">
                    <p>{{calendarTimeInitData.translation.from}}</p>
                    <h4>{{timeStart}}</h4>
                  </div>
                   <div class="form-element till">
                    <p>{{calendarTimeInitData.translation.to}}</p>
                    <h4>{{timeEnd}}</h4>
                  </div>
                  <div class="form-element cafe">
                    <p>{{calendarTimeInitData.translation.in}}</p>
                    <h4>{{calendarTimeInitData.translation.cafe}}</h4>
                  </div>

                  <div class="c0ffee-button" id="change">
                      <a @click="change"><h4>{{calendarTimeInitData.translation.changeButton}}</h4> </a>
                  </div>
                </div>

                <div class="input-form">
                  <div class="input-element name">
                    <input
                      class="name"
                      :placeholder="calendarTimeInitData.translation.name"
                      v-model="name"
                      type="text"
                    />
                    <span class="icon">
                      <i 
                        v-show="(this.name === '')" 
                        class="tremtr-icon-uniF101" 
                        style="color: #1e1914;">
                      </i>
                      <i 
                        v-show="(this.name != '')" 
                        class="tremtr-icon-uniF101" 
                        style="color: green;"
                      ></i>
                    </span>
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
                    <span class="icon">
                      <i 
                        v-show="errors.has('email')" 
                        class="tremtr-icon-uniF10B" 
                        style="color: red;">
                      </i>
                      <i 
                        v-show="!errors.has('email') && (this.email != '')" 
                        class="tremtr-icon-uniF10B" 
                        style="color: green;"
                      ></i>
                      <i 
                        v-show="!errors.has('email') && (this.email === '')" 
                        class="tremtr-icon-uniF10B" 
                        style="color: #1e1914;"
                      ></i>
                    </span>
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
                    <span class="icon">
                      <i 
                        v-show="errors.has('phone')" 
                        class=" tremtr-icon-uniF105" 
                        style="color: red;"
                      ></i>
                      <i 
                        v-show="!errors.has('phone') && (this.phone != '')" 
                        class=" tremtr-icon-uniF105" 
                        style="color: green;"
                      ></i>
                      <i 
                        v-show="!errors.has('phone') && (this.phone === '')" 
                        class=" tremtr-icon-uniF105" 
                        style="color: #1e1914;"
                      ></i>
                    </span>
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
                    <span class="icon">
                      <i 
                        v-show="this.message === ''" 
                        class="tremtr-icon-uniF109" 
                        style="color: #1e1914;"
                      ></i>
                      <i 
                        v-show="this.message !== ''" 
                        class="tremtr-icon-uniF109" 
                        style="color: green;"
                      ></i>
                    </span>
                  </div>
              
                  <div class="c0ffee-button" id="confirm">
                      <a @click="confirm"><h4>{{calendarTimeInitData.translation.confirmButton}}</h4></a>
                  </div> 
                </div>

              </div>
            
          </div>
      </div>
    </transition>
     <transition name="fade" mode="in-out">
      <div class="reservation3" v-if="view === 2">
        <div class="confirmation">
          <img src="./assets/227109-coffee-shop.png">
          <h2>{{firstName}}</h2>
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

// Flatpickr.localize(FlatpickrI18n.ru);


// import 'fabric';



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

    document.getElementById('reservation').style.setProperty('--trem-main-color', this.calendarTimeInitData.mainColor)
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
      return moment(this.date, this.momentDateFormat).format('dddd').toLowerCase()
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
          if (open.date === moment(this.date, this.momentDateFormat).format(this.dbDateFormatForMoment)) { 
            timeStart = open.time.start
            timeFinish = open.time.end 
          }
        }

        this.openHoursStart = timeStart;
        this.openHoursEnd = timeFinish;

        if (moment().date() === moment(this.date, this.momentDateFormat).date()) {
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
        if (open.date === moment(this.date, this.momentDateFormat).format(this.dbDateFormatForMoment)) { 
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
        if (this.timeEnd === '') {
          this.timeEnd = moment(this.timeStart, this.momentTimeFormat).add(Number(60), 'm').format(this.momentTimeFormat)
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
    test () {
      
    },
    confirm () {
      if ((this.name !== '') && (this.mail !== '') && (this.phone !== '') && (!this.errors.has('email')) && (!this.errors.has('phone'))) {
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
        


        //add id to canvas objects and lock objects
        
        let objCounter = 1;
        let counter = 0;
        let canvasObjectId = 0;

        for(let i = 0; i < this.canvas.getObjects().length; i++){
          
          this.canvas.item(i).id = canvasObjectId;
          counter ++;

          this.canvas.item(i).lockMovementX = true; 
          this.canvas.item(i).lockMovementY = true; 
          this.canvas.item(i).lockScalingX = true;
          this.canvas.item(i).lockScalingY = true;
          this.canvas.item(i).lockUniScaling = true;
          this.canvas.item(i).lockRotation = true; 
          this.canvas.item(i).hasControls = false;
          this.canvas.item(i).hoverCursor = 'pointer';
          this.canvas.item(i).hasBorders = false;
          this.canvas.item(i).opacity = 0;
          this.canvas.item(i).selectable = false;
          this.canvas.item(i).evented = false;


          if (counter%5 === 0) {

            let tablePeople = [this.canvas.item(i-4).text, this.canvas.item(i-3).text]
            this.canvas.item(i).name = tablePeople;

            this.canvas.item(i).set({
              fill: fillActive,
              strokeWidth : 0,
              opacity: 1
            });

            this.canvas.item(i-4).set({
              opacity: 1,
              left: this.canvas.item(i).left-this.canvas.item(i).width/2 + 8,
              top: this.canvas.item(i).top-this.canvas.item(i).height/2 + 8,
              fontSize: 20,
              fontWeight: 'bold'
            });

            this.canvas.item(i-2).set({
              fontFamily:"trem-reservation",
              text:"",
              left: this.canvas.item(i).left+this.canvas.item(i).width/2 - 25,
              top: this.canvas.item(i).top+this.canvas.item(i).height/2 - 25 ,
              fontSize: 20
            });

            this.canvas.item(i-2).moveTo(i)
            this.canvas.item(i-4).moveTo(i)
            // this.canvas.item(i).moveTo(i-4)

            this.canvas.renderAll()
            canvasObjectId++;
          }
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
              if (_self.canvas.item(_self.canvas.getObjects().indexOf(e.target)+1).opacity != 1) {
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
                _self.canvas.item(i+1).set({
                  opacity: 0
                });
              }
            }

            if (e.target.type === 'rect') {
              let index = _self.canvas.getObjects().indexOf(e.target)

              e.target.set({
                fill: fillHover
              });

              _self.canvas.item(_self.canvas.getObjects().indexOf(e.target)+1).set({
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

              e.target.set({
                fontFamily:"trem-reservation",
                text:""
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

        let todaysReservations = this.reservations.filter(reservation => reservation.tremtr_reservation_date === this.date);

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
      
      for(let i = 2; i < this.canvas.getObjects().length; i= i+5){
        if (this.disabledTables.includes(this.canvas.item(i).name[0])) {
          this.canvas.item(i).set({
            fill: this.fillBooked,
            evented: false
          });

          if (this.canvas.item(i).name[0] === this.table) {
            this.canvas.item(i).set({
              fill: this.fillActive,
              evented: false
            });
            this.canvas.item(i+1).set({
              opacity: 0
            });
            this.table = ''
          }
        }



        if (!(this.disabledTables.includes(this.canvas.item(i).name[0]))) {
          if (this.canvas.item(i).fill === this.fillBooked) {
            this.canvas.item(i).set({
              fill: this.fillActive,
              evented: false
            });
          }
        }
      }
      this.canvas.renderAll()
    },

    selectTable () {
      
      for(let i = 2; i < this.canvas.getObjects().length; i= i+5){
        if (this.canvas.item(i).name[0] === this.table) {
          this.canvas.item(i).set({
            fill: this.fillHover
          });
          this.canvas.item(i+1).set({
            opacity: 1
          });
        }
      }
      this.canvas.renderAll()
    },

    makeTablesSelectable () {

        if (this.canvas !== ''){
            if ((this.date !== '') && (this.timeEnd !== '') && (this.timeStart !== '')) {
              for(let i = 2; i < this.canvas.getObjects().length; i= i+5){

                if (!(this.disabledTables.includes(this.canvas.item(i).name[0]))) { 

                  this.canvas.item(i).set({
                    evented: true
                  });
                }

              }
              this.canvas.renderAll()
            } else {
              for(let i = 2; i < this.canvas.getObjects().length; i= i+5){

                if (!(this.disabledTables.includes(this.canvas.item(i).name[0]))) {

                  this.canvas.item(i).set({
                    evented: false
                  });
                  this.canvas.item(i+1).set({
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
  @import url(https://fonts.googleapis.com/css?family=Work+Sans:300,400,600);
  $second-font: 'Work Sans', sans-serif;

  .toast-container
  {
    position:fixed !important;
    top: 30% !important;
    font-family: inherit;
    .toast
    {
      text-align: center;
      border-radius: 5px;
      background-color: rgba(30,25,20,0.7) !important; 
      font-size: 24px;
      &:before
      {
        margin:auto 0.5em;
        font-family: 'trem-reservation';
        content: "\f10d";
      }
    }

  }

</style>


<style scoped>

  span input{
    max-width: 30px;
  }

  .reservation {
    --trem-main-color: #c0ffee;
  }

  .reservation {
    all: initial;  
  }

  .reservation  * :not(input){
    all: unset;  

    font-family: inherit !important;
    font-size: inherit !important;
    font-weight: inherit !important;
  }

  .reservation h4  {
    font-family: inherit  !important;
    font-size: inherit !important;
    font-weight: inherit !important;
   
  }

  .reservation h2  {
    font-family: inherit  !important;
    font-size: x-large !important;
    font-weight:bold !important;
   
  }


  /*.reservation ::before,
  .reservation ::after,
  .reservation  *::before,
  .reservation  *::after {
    all: unset;
  }
  */

  textarea 
  {
      width: 11em !important;
      font-family: inherit !important;
      font-size: 14px !important;

  }
  h4 
  {
      font-family: inherit !important;
  }body 
  {
      margin: 0;
  }.reservation 
  {
      font-family: inherit;

      z-index: 99999;

      display: -webkit-box;
      display: -ms-flexbox;
      display: flex;

      width: 90%;
      margin: 1em auto;

      -webkit-box-pack: center;
      -ms-flex-pack: center;
      justify-content: center;
      -webkit-box-align: center;
      -ms-flex-align: center;
      align-items: center;

      background-color: white;
  }

  .reservation  a{
      text-transform: uppercase;
  }

  .reservation  h2{
      text-transform: uppercase;
  }

  .reservation .reservation1  h4{
      text-transform: uppercase;
  }

  .reservation .reservation2  p{
      text-transform: uppercase;
  }

  .reservation .hint {
      display: flex;
      flex-flow: column;
      margin: 1em auto; 
      width: 100%; 
      text-align: center;
      border: 2px dashed var(--trem-main-color);
  }
  .reservation .reservation1 
  {
      width: 100%;
      margin: auto;

      border: 1px solid var(--trem-main-color);
  }.reservation .reservation1 .envelope 
  {
      position: relative;
      margin: auto;

      display: grid;
      grid-template-rows: auto auto auto;

      justify-content: center;
  }.reservation .reservation1 .envelope .exit 
  {
      position: absolute;
      top: .5em;
      right: 1em; 

      color: transparent;
  }.reservation .reservation1 .envelope .header-center 
  {
      margin: 1em auto;

      height: fit-content;

      text-align: center; 

      color: #1f1a14;
  }.reservation .reservation1 .envelope p 
  {
      width: 14em;
      margin: .5em auto 0;

      text-align: left;
  }.reservation .reservation1 .envelope .opening-hours 
  {

      margin: 0 auto .5em auto;
  }.reservation .reservation1 .envelope .people-form 
  {
    width: 100%;
    margin: auto;
    display: grid;

    grid-template-rows: auto auto auto auto  auto auto auto;

    justify-content: center;
    justify-items: center;

  }.reservation .reservation1 .envelope .people-form .form-element 
  {
      position: relative;

      width: 225px;
      height: 32px;

      margin: auto auto 1em auto;

      border: 1px solid #1f1a14;
      border-radius: 0;
      background: #fff; 

  }.reservation .reservation1 .envelope .people-form .form-element flat-pickr ,
  .reservation .reservation1 .envelope .people-form .form-element input ,
  .reservation .reservation1 .envelope .people-form .form-element select ,
  .reservation .reservation1 .envelope .people-form .form-element textarea 
  {
      font-family: inherit !important;
      font-size: 16px;

      width: 225px !important;

      cursor: pointer;
      text-indent: 1px;

      border: none !important;
      outline: none !important;
      background: transparent;
      z-index: 2;

      -webkit-appearance: none;
         -moz-appearance: none;
  }.reservation .reservation1 .envelope .people-form .form-element flat-pickr ::placeholder,
  .reservation .reservation1 .envelope .people-form .form-element input ::placeholder,
  .reservation .reservation1 .envelope .people-form .form-element select ::placeholder
  {
      color: #000;
  }.reservation .reservation1 .envelope .people-form .form-element flat-pickr ::-webkit-input-placeholder,
  .reservation .reservation1 .envelope .people-form .form-element input ::-webkit-input-placeholder,
  .reservation .reservation1 .envelope .people-form .form-element select ::-webkit-input-placeholder
  {
      color: #000;
  }.reservation .reservation1 .envelope .people-form .form-element flat-pickr :-moz-placeholder,
  .reservation .reservation1 .envelope .people-form .form-element flat-pickr ::-moz-placeholder,
  .reservation .reservation1 .envelope .people-form .form-element input :-moz-placeholder,
  .reservation .reservation1 .envelope .people-form .form-element input ::-moz-placeholder,
  .reservation .reservation1 .envelope .people-form .form-element select :-moz-placeholder,
  .reservation .reservation1 .envelope .people-form .form-element select ::-moz-placeholder
  {
      opacity: 1; 
      color: #000;
  }.reservation .reservation1 .envelope .people-form .form-element flat-pickr :-ms-input-placeholder,
  .reservation .reservation1 .envelope .people-form .form-element flat-pickr ::-ms-input-placeholder,
  .reservation .reservation1 .envelope .people-form .form-element input :-ms-input-placeholder,
  .reservation .reservation1 .envelope .people-form .form-element input ::-ms-input-placeholder,
  .reservation .reservation1 .envelope .people-form .form-element select :-ms-input-placeholder,
  .reservation .reservation1 .envelope .people-form .form-element select ::-ms-input-placeholder
  {
      color: #000;
  }.reservation .reservation1 .envelope .people-form .form-element .book-icon 
  {
      position: absolute;
      right: 0.3em;
      top: 6px;


      background: transparent;
      z-index:1;

  }.reservation .reservation1 .envelope .people-form canvas 
  {

      border: 1px solid #1f1a14;
  }.reservation .reservation1 .envelope .people-form .c0ffee-button 
  {
      display: inline-block;

      height: 30%;
  }.reservation .reservation1 .envelope .people-form .c0ffee-button a 
  {
      position: relative;

      display: inline-block;

      margin: 1em 0;
      padding: .5em 0;

      cursor: pointer;
      text-align: center;
  }.reservation .reservation1 .envelope .people-form .c0ffee-button a h4 
  {
      margin: auto;
  }.reservation .reservation1 .envelope .people-form .c0ffee-button .c0ffee-button-skin ,
  .reservation .reservation1 .envelope .people-form .c0ffee-button a ,
  .reservation .reservation1 .reservation2 .envelope .grid-container .info-form .people-form .c0ffee-button a ,
  .reservation .reservation1 .reservation2 .envelope .grid-container .input-form .people-form .c0ffee-button a ,
  .reservation .reservation1 .reservation2 .envelope .people-form .grid-container .info-form .c0ffee-button a ,
  .reservation .reservation1 .reservation2 .envelope .people-form .grid-container .input-form .c0ffee-button a ,
  .reservation .reservation2 .reservation1 .envelope .grid-container .info-form .people-form .c0ffee-button a ,
  .reservation .reservation2 .reservation1 .envelope .grid-container .input-form .people-form .c0ffee-button a ,
  .reservation .reservation2 .reservation1 .envelope .people-form .grid-container .info-form .c0ffee-button a ,
  .reservation .reservation2 .reservation1 .envelope .people-form .grid-container .input-form .c0ffee-button a 
  {
      width: 14em; 

      text-decoration: none;

      color: #000;
      border: 2px solid var(--trem-main-color);
      border-radius: 5px;
      background: var(--trem-main-color);
  }.reservation .reservation1 .envelope .people-form .c0ffee-button .c0ffee-button-skin :hover,
  .reservation .reservation1 .envelope .people-form .c0ffee-button a :hover,
  .reservation .reservation1 .reservation2 .envelope .grid-container .info-form .people-form .c0ffee-button a :hover,
  .reservation .reservation1 .reservation2 .envelope .grid-container .input-form .people-form .c0ffee-button a :hover,
  .reservation .reservation1 .reservation2 .envelope .people-form .grid-container .info-form .c0ffee-button a :hover,
  .reservation .reservation1 .reservation2 .envelope .people-form .grid-container .input-form .c0ffee-button a :hover,
  .reservation .reservation2 .reservation1 .envelope .grid-container .info-form .people-form .c0ffee-button a :hover,
  .reservation .reservation2 .reservation1 .envelope .grid-container .input-form .people-form .c0ffee-button a :hover,
  .reservation .reservation2 .reservation1 .envelope .people-form .grid-container .info-form .c0ffee-button a :hover,
  .reservation .reservation2 .reservation1 .envelope .people-form .grid-container .input-form .c0ffee-button a :hover
  {
      color: #fff;
      border: 2px solid transparent;
      background: #1f1a14;
  }.reservation .reservation1 .envelope .people-form .c0ffee-button .c0ffee-button-skin:hover h4 ,
  .reservation .reservation1 .envelope .people-form .c0ffee-button a:hover h4 ,
  .reservation .reservation1 .reservation2 .envelope .grid-container .info-form .people-form .c0ffee-button a:hover h4 ,
  .reservation .reservation1 .reservation2 .envelope .grid-container .input-form .people-form .c0ffee-button a:hover h4 ,
  .reservation .reservation1 .reservation2 .envelope .people-form .grid-container .info-form .c0ffee-button a:hover h4 ,
  .reservation .reservation1 .reservation2 .envelope .people-form .grid-container .input-form .c0ffee-button a:hover h4 ,
  .reservation .reservation2 .reservation1 .envelope .grid-container .info-form .people-form .c0ffee-button a:hover h4 ,
  .reservation .reservation2 .reservation1 .envelope .grid-container .input-form .people-form .c0ffee-button a:hover h4 ,
  .reservation .reservation2 .reservation1 .envelope .people-form .grid-container .info-form .c0ffee-button a:hover h4 ,
  .reservation .reservation2 .reservation1 .envelope .people-form .grid-container .input-form .c0ffee-button a:hover h4 
  {
      color: #fff;
  }.reservation .reservation2 
  {
      width: 100%;
      margin: auto;

      border: 1px solid var(--trem-main-color);
  }.reservation .reservation2 .envelope 
  {
      display: -webkit-box;
      display: -ms-flexbox;
      display: flex;

      width: 100%;
      height: 100%;

      -webkit-box-orient: vertical;
      -webkit-box-direction: normal;
      -ms-flex-flow: column;
          flex-flow: column;
      -webkit-box-align: center;
      -ms-flex-align: center;
      align-items: center;
  }.reservation .reservation2 .envelope h4 
  {
      color: #1f1a14;
  }.reservation .reservation2 .envelope .header-center 
  {
      margin: 1em auto;

      color: #1f1a14;
  }.reservation .reservation2 .envelope .grid-container 
  {
      display: grid;

      grid-template-columns: 14em 14em;
      grid-column-gap: 6em;
      -webkit-box-pack: center;
      -ms-flex-pack: center;
      justify-content: center;
  }.reservation .reservation2 .envelope .grid-container .info-form 
  {
      display: grid;

      grid-template-rows: 35px 35px 35px 35px 35px 35px 100px;
      grid-row-gap: 5px;
      -ms-flex-line-pack: start;
      align-content: start;
      justify-items: start;
  }.reservation .reservation2 .envelope .grid-container .info-form .form-element 
  {
      display: grid;

      grid-template-columns: 7em 7em;
      justify-items: start;
      -webkit-box-align: center;
      -ms-flex-align: center;
      align-items: center;
  }.reservation .reservation2 .envelope .grid-container .info-form .form-element h4 ,
  .reservation .reservation2 .envelope .grid-container .info-form .form-element p 
  {
      margin: 0;
  }.reservation .reservation2 .envelope .grid-container .info-form .c0ffee-button 
  {
      display: -webkit-box;
      display: -ms-flexbox;
      display: flex;

      -webkit-box-align: center;
      -ms-flex-align: center;
      align-items: center;
  }.reservation .reservation2 .envelope .grid-container .info-form .c0ffee-button a 
  {
      position: relative;

      display: inline-block;

      width: 14em;
      height: 20px;
      margin: 0;
      padding: .5em 0;

      cursor: pointer;
      text-align: center;
  }.reservation .reservation2 .envelope .grid-container .info-form .c0ffee-button a h4 
  {
      margin: auto;
  }.reservation .reservation1 .reservation2 .envelope .grid-container .info-form .people-form .c0ffee-button a ,
  .reservation .reservation1 .reservation2 .envelope .people-form .grid-container .info-form .c0ffee-button a ,
  .reservation .reservation2 .envelope .grid-container .info-form .c0ffee-button .c0ffee-button-skin ,
  .reservation .reservation2 .envelope .grid-container .info-form .c0ffee-button a ,
  .reservation .reservation2 .envelope .grid-container .info-form .input-form .c0ffee-button a ,
  .reservation .reservation2 .envelope .grid-container .input-form .info-form .c0ffee-button a ,
  .reservation .reservation2 .reservation1 .envelope .grid-container .info-form .people-form .c0ffee-button a ,
  .reservation .reservation2 .reservation1 .envelope .people-form .grid-container .info-form .c0ffee-button a 
  {
      text-decoration: none;

      color: #000;
      border: none;
      border-radius: 5px; 
      background-color: rgba(30,25,20,.2);
  }.reservation .reservation1 .reservation2 .envelope .grid-container .info-form .people-form .c0ffee-button a :hover,
  .reservation .reservation1 .reservation2 .envelope .people-form .grid-container .info-form .c0ffee-button a :hover,
  .reservation .reservation2 .envelope .grid-container .info-form .c0ffee-button .c0ffee-button-skin :hover,
  .reservation .reservation2 .envelope .grid-container .info-form .c0ffee-button a :hover,
  .reservation .reservation2 .envelope .grid-container .info-form .input-form .c0ffee-button a :hover,
  .reservation .reservation2 .envelope .grid-container .input-form .info-form .c0ffee-button a :hover,
  .reservation .reservation2 .reservation1 .envelope .grid-container .info-form .people-form .c0ffee-button a :hover,
  .reservation .reservation2 .reservation1 .envelope .people-form .grid-container .info-form .c0ffee-button a :hover
  {
      color: #fff;
      border: none;
      background: #1f1a14;
  }.reservation .reservation1 .reservation2 .envelope .grid-container .info-form .people-form .c0ffee-button a:hover h4 ,
  .reservation .reservation1 .reservation2 .envelope .people-form .grid-container .info-form .c0ffee-button a:hover h4 ,
  .reservation .reservation2 .envelope .grid-container .info-form .c0ffee-button .c0ffee-button-skin:hover h4 ,
  .reservation .reservation2 .envelope .grid-container .info-form .c0ffee-button a:hover h4 ,
  .reservation .reservation2 .envelope .grid-container .info-form .input-form .c0ffee-button a:hover h4 ,
  .reservation .reservation2 .envelope .grid-container .input-form .info-form .c0ffee-button a:hover h4 ,
  .reservation .reservation2 .reservation1 .envelope .grid-container .info-form .people-form .c0ffee-button a:hover h4 ,
  .reservation .reservation2 .reservation1 .envelope .people-form .grid-container .info-form .c0ffee-button a:hover h4 
  {
      color: #fff;
  }.reservation .reservation2 .envelope .grid-container .input-form 
  {
      display: grid;

      grid-template-rows: 35px 35px 35px 115px 100px;
      grid-row-gap: 5px;
  }.reservation .reservation2 .envelope .grid-container .input-form .input-element 
  {
      display: -webkit-box;
      display: -ms-flexbox;
      display: flex;

      width: 14em;
      height: 30px; 

      border: 1px solid #1f1a14;

      -webkit-box-align: center;
      -ms-flex-align: center;
      align-items: center;
  }.reservation .reservation2 .envelope .grid-container .input-form .input-element input 
  {
      width: 180px;
      height: 28px;

      font-size: 14px;

      cursor: pointer;
      text-align: left;
      text-indent: 2em;

      border: none; 
      outline: none;

      -webkit-appearance: none;
         -moz-appearance: none;
  }.reservation .reservation2 .envelope .grid-container .input-form .input-element input ::placeholder
  {
      font-style: italic; 

      color: #000;
  }.reservation .reservation2 .envelope .grid-container .input-form .input-element input ::-webkit-input-placeholder
  {
      font-style: italic; 

      color: #000;
  }.reservation .reservation2 .envelope .grid-container .input-form .input-element input :-moz-placeholder,
  .reservation .reservation2 .envelope .grid-container .input-form .input-element input ::-moz-placeholder
  {
      font-style: italic;

      opacity: 1; 
      color: #000;
  }.reservation .reservation2 .envelope .grid-container .input-form .input-element input :-ms-input-placeholder,
  .reservation .reservation2 .envelope .grid-container .input-form .input-element input ::-ms-input-placeholder
  {
      font-style: italic; 

      color: #000;
  }.reservation .reservation2 .envelope .grid-container .input-form .input-element .icon 
  {
      display: -webkit-box;
      display: -ms-flexbox;
      display: flex;

      width: 2em;
      height: 30px;
      margin: 0 0.3em 0 auto;

      background: #fff; 

      -webkit-box-align: center;
      -ms-flex-align: center;
      align-items: center;
      -webkit-box-pack: end;
      -ms-flex-pack: end;
      justify-content: flex-end;
  }.reservation .reservation2 .envelope .grid-container .input-form .input-element .icon i 
  {
      margin: auto;
  }.reservation .reservation2 .envelope .grid-container .input-form .message 
  {
      height: 100px;

      -webkit-box-align: start;
      -ms-flex-align: start;
      align-items: flex-start;
  }.reservation .reservation2 .envelope .grid-container .input-form .message textarea 
  {
      width: 14em;
      height: 95px;

      font-size: 14px !important;

      resize: none; 
      cursor: pointer;
      text-align: left;
      text-indent: 2em;

      border: none;
      outline: none;

      -webkit-appearance: none;
         -moz-appearance: none;
  }.reservation .reservation2 .envelope .grid-container .input-form .message textarea ::placeholder
  {
      font-style: italic; 

      color: #000;
  }.reservation .reservation2 .envelope .grid-container .input-form .message textarea ::-webkit-input-placeholder
  {
      font-style: italic; 

      color: #000;
  }.reservation .reservation2 .envelope .grid-container .input-form .message textarea :-moz-placeholder,
  .reservation .reservation2 .envelope .grid-container .input-form .message textarea ::-moz-placeholder
  {
      font-style: italic;

      opacity: 1; 
      color: #000;
  }.reservation .reservation2 .envelope .grid-container .input-form .message textarea :-ms-input-placeholder,
  .reservation .reservation2 .envelope .grid-container .input-form .message textarea ::-ms-input-placeholder
  {
      font-style: italic; 

      color: #000;
  }.reservation .reservation2 .envelope .grid-container .input-form .c0ffee-button 
  {
      display: -webkit-box;
      display: -ms-flexbox;
      display: flex;

      -webkit-box-align: center;
      -ms-flex-align: center;
      align-items: center;
  }.reservation .reservation2 .envelope .grid-container .input-form .c0ffee-button a 
  {
      position: relative;

      display: inline-block;

      width: 14em;
      height: 20px;
      margin: 0;
      padding: .5em 0;

      cursor: pointer;
      text-align: center;
  }.reservation .reservation2 .envelope .grid-container .input-form .c0ffee-button a h4 
  {
      margin: auto;
  }.reservation .reservation1 .reservation2 .envelope .grid-container .input-form .people-form .c0ffee-button a ,
  .reservation .reservation1 .reservation2 .envelope .people-form .grid-container .input-form .c0ffee-button a ,
  .reservation .reservation2 .envelope .grid-container .info-form .input-form .c0ffee-button a ,
  .reservation .reservation2 .envelope .grid-container .input-form .c0ffee-button .c0ffee-button-skin ,
  .reservation .reservation2 .envelope .grid-container .input-form .c0ffee-button a ,
  .reservation .reservation2 .envelope .grid-container .input-form .info-form .c0ffee-button a ,
  .reservation .reservation2 .reservation1 .envelope .grid-container .input-form .people-form .c0ffee-button a ,
  .reservation .reservation2 .reservation1 .envelope .people-form .grid-container .input-form .c0ffee-button a 
  {
      text-decoration: none;

      color: #000;
      border-radius: 5px; 
      background: none;
      background: var(--trem-main-color);
  }.reservation .reservation1 .reservation2 .envelope .grid-container .input-form .people-form .c0ffee-button a :hover,
  .reservation .reservation1 .reservation2 .envelope .people-form .grid-container .input-form .c0ffee-button a :hover,
  .reservation .reservation2 .envelope .grid-container .info-form .input-form .c0ffee-button a :hover,
  .reservation .reservation2 .envelope .grid-container .input-form .c0ffee-button .c0ffee-button-skin :hover,
  .reservation .reservation2 .envelope .grid-container .input-form .c0ffee-button a :hover,
  .reservation .reservation2 .envelope .grid-container .input-form .info-form .c0ffee-button a :hover,
  .reservation .reservation2 .reservation1 .envelope .grid-container .input-form .people-form .c0ffee-button a :hover,
  .reservation .reservation2 .reservation1 .envelope .people-form .grid-container .input-form .c0ffee-button a :hover
  {
      color: #fff;
      background: #1f1a14;
  }.reservation .reservation1 .reservation2 .envelope .grid-container .input-form .people-form .c0ffee-button a:hover h4 ,
  .reservation .reservation1 .reservation2 .envelope .people-form .grid-container .input-form .c0ffee-button a:hover h4 ,
  .reservation .reservation2 .envelope .grid-container .info-form .input-form .c0ffee-button a:hover h4 ,
  .reservation .reservation2 .envelope .grid-container .input-form .c0ffee-button .c0ffee-button-skin:hover h4 ,
  .reservation .reservation2 .envelope .grid-container .input-form .c0ffee-button a:hover h4 ,
  .reservation .reservation2 .envelope .grid-container .input-form .info-form .c0ffee-button a:hover h4 ,
  .reservation .reservation2 .reservation1 .envelope .grid-container .input-form .people-form .c0ffee-button a:hover h4 ,
  .reservation .reservation2 .reservation1 .envelope .people-form .grid-container .input-form .c0ffee-button a:hover h4 
  {
      color: #fff;
  }.reservation .fade-enter-active ,
  .reservation .fade-leave-active 
  {
      transition: opacity .5s;
  }.reservation .fade-enter ,
  .reservation .fade-leave-to 
  {
      opacity: 0;
  }.reservation .reservation3 
  {
      position: fixed;
      z-index: 999999;
      top: 50%;
      left: 50%;

      width: 40%;
      max-width: 500px;
      height: 40%;
      max-height: 400px;
      margin: auto; 

      -webkit-transform: translate(-50%,-50%);
              transform: translate(-50%,-50%);

      border: 2px solid #5e5448;
      border-radius: 10px;
      background: #fff;
  }.reservation .reservation3 .confirmation ,
  .reservation .reservation3 
  {
      display: -webkit-box;
      display: -ms-flexbox;
      display: flex;

      -webkit-box-pack: center;
      -ms-flex-pack: center;
      justify-content: center;
      -webkit-box-align: center;
      -ms-flex-align: center;
      align-items: center;
  }.reservation .reservation3 .confirmation 
  {
      text-align: center;

      -webkit-box-orient: vertical;
      -webkit-box-direction: normal;
      -ms-flex-flow: column wrap;
          flex-flow: column wrap;
  }.reservation .reservation3 .confirmation h2 
  {
      font-family: inherit;
      font-weight: 400;

      margin: 1em auto auto;
  }.reservation .reservation3 .confirmation hr 
  {
      width: 70%;
      margin: 2em auto auto; 

      border-top: 3px solid #8e8c89;
  }

  @media(max-width:10000px)
  {

    input 
    {
        height: 35px !important;
        width: 225px !important;

        margin: 0 !important;
        padding: 0 0 0 0.5em !important;

        cursor: pointer !important;
        text-indent: 1px !important;
        border: none !important;

        color: #1e1914 !important;
        background: transparent !important;
    }

    textarea 
    {
        height: 95px !important;


        resize: none !important; 
        cursor: pointer !important;
        text-align: left !important;
        text-indent: 1px !important;
        padding: 0 0 0 0.5em !important;

        border: none !important;
        outline: none !important;

        -webkit-appearance: none !important;
        -moz-appearance: none!important;

        word-break: break-all !important;
    }

   

    input ,
    p ,
    select 
    {
        font-family: inherit;
        font-size: 16px;
    }



    .reservation 
    {
        z-index: 99999; 

        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;

        margin: 1em auto;

        -webkit-box-pack: center;
        -ms-flex-pack: center;
        justify-content: center;
        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center;
    }



    .reservation .reservation2 
    {
        width: 100%;
        margin: auto;

        border: 1px solid var(--trem-main-color);
    }.reservation .reservation2 .envelope 
    {
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;

        width: 100%;
        height: 100%;
        
        -webkit-box-orient: vertical;
        -webkit-box-direction: normal;
        -ms-flex-flow: column;
            flex-flow: column;
        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center;
    }.reservation .reservation2 .envelope h4 
    {
        color: #1f1a14;
    }.reservation .reservation2 .envelope .header-center 
    {
        margin: 1em auto;

        color: #1f1a14;
    }.reservation .reservation2 .envelope .grid-container 
    {
        display: grid;

        grid-template-columns: 100%;
        grid-template-rows: 1fr 1fr;
        -webkit-box-pack: center;
        -ms-flex-pack: center;
        justify-content: center;
    }.reservation .reservation2 .envelope .grid-container .info-form 
    {
        display: grid;

        grid-template-rows: 35px 35px 35px 35px 35px 35px 70px;
        grid-row-gap: 5px;
        -ms-flex-line-pack: start;
        align-content: start;
        justify-items: center;
    }.reservation .reservation2 .envelope .grid-container .info-form .form-element 
    {
        display: grid;

        grid-template-columns: 120px 120px;
        grid-column-gap: 0;
        grid-row-gap: 0;
        justify-items: center;
        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center;
    }.reservation .reservation2 .envelope .grid-container .info-form .form-element h4 ,
    .reservation .reservation2 .envelope .grid-container .info-form .form-element p 
    {
        margin: 0;
    }.reservation .reservation2 .envelope .grid-container .input-form 
    {
        display: grid;
        
        grid-template-rows: 35px 35px 35px 115px 70px;
        grid-row-gap: 5px;
        justify-items: center;
    }.reservation .reservation2 .envelope .grid-container .input-form .input-element 
    {
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;

        width: 14em;
        height: 30px; 

        border: 1px solid #1f1a14;

        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center;

        overflow: hidden;
    }.reservation .reservation2 .envelope .grid-container .input-form .input-element input 
    {
        width: 100% !important;
        height: 28px;

        cursor: pointer;
        text-align: left;
        text-indent: 2em;

        border: none; 
        outline: none;

        -webkit-appearance: none;
           -moz-appearance: none;
    }.reservation .reservation2 .envelope .grid-container .input-form .input-element input ::placeholder
    {
        font-style: italic; 

        color: #000;
    }.reservation .reservation2 .envelope .grid-container .input-form .input-element input ::-webkit-input-placeholder
    {
        font-style: italic; 

        color: #000;
    }.reservation .reservation2 .envelope .grid-container .input-form .input-element input :-moz-placeholder,
    .reservation .reservation2 .envelope .grid-container .input-form .input-element input ::-moz-placeholder
    {
        font-style: italic;

        opacity: 1; 
        color: #000;
    }.reservation .reservation2 .envelope .grid-container .input-form .input-element input :-ms-input-placeholder,
    .reservation .reservation2 .envelope .grid-container .input-form .input-element input ::-ms-input-placeholder
    {
        font-style: italic; 

        color: #000;
    }.reservation .reservation2 .envelope .grid-container .input-form .input-element .icon 
    {
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;

        width: 2em;
        height: 30px;
        margin: 0 0.3em 0 auto;

        background: #fff; 

        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center;
        -webkit-box-pack: end;
        -ms-flex-pack: end;
        justify-content: flex-end;
    }.reservation .reservation2 .envelope .grid-container .input-form .input-element .icon i 
    {
        margin: auto;
    }.reservation .reservation2 .envelope .grid-container .input-form .message 
    {
        height: 100px;

        -webkit-box-align: start;
        -ms-flex-align: start;
        align-items: flex-start;
    }.reservation .reservation2 .envelope .grid-container .input-form .message textarea 
    {
        padding-top: 5px !important;

        width: 14em;
        height: 95px;

        resize: none; 
        cursor: pointer;
        text-align: left;
        text-indent: 2em;

        border: none;
        outline: none;

        background-color: transparent;

        -webkit-appearance: none;
           -moz-appearance: none;
    }.reservation .reservation2 .envelope .grid-container .input-form .message textarea ::placeholder
    {
        font-style: italic; 

        color: #000;
    }.reservation .reservation2 .envelope .grid-container .input-form .message textarea ::-webkit-input-placeholder
    {
        font-style: italic; 

        color: #000;
    }.reservation .reservation2 .envelope .grid-container .input-form .message textarea :-moz-placeholder,
    .reservation .reservation2 .envelope .grid-container .input-form .message textarea ::-moz-placeholder
    {
        font-style: italic;

        opacity: 1; 
        color: #000;
    }.reservation .reservation2 .envelope .grid-container .input-form .message textarea :-ms-input-placeholder,
    .reservation .reservation2 .envelope .grid-container .input-form .message textarea ::-ms-input-placeholder
    {
        font-style: italic; 

        color: #000;
    }.reservation .reservation3 
    {
        position: fixed;
        z-index: 999999;
        top: 50%;
        left: 50%;

        width: 60%;
        max-width: 320px;
        height: 60%;
        max-height: 400px;
        margin: auto; 

        border: 4px solid #5e5448;
        border-radius: 10px;
        background: #fff;
    }.reservation .reservation3 .confirmation ,
    .reservation .reservation3 
    {
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;

        -webkit-box-pack: center;
        -ms-flex-pack: center;
        justify-content: center;
        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center;
    }.reservation .reservation3 .confirmation 
    {
        z-index: 9999999;

        visibility: visible; 

        text-align: center;

        -webkit-box-orient: vertical;
        -webkit-box-direction: normal;
        -ms-flex-flow: column;
            flex-flow: column;
    }.reservation .reservation3 .confirmation h2 
    {
        font-family: inherit;
        font-weight: 400;

        z-index: 99999999;

        margin: 1em auto auto;
    }.reservation .reservation3 .confirmation hr 
    {
        display: none;
        z-index: 99999999;

        width: 70%;
        margin: 2em auto auto; 

        border-top: 3px solid #8e8c89;
    }}
</style>

