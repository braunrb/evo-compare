/**
 * Created by admin on 12.04.2017.
 */
var id = 21;

$('#add').on('click',function () {
    addInCompare(id)
    $('.to-compare[data-id="'+id+'"]').addClass('active')
})

$('#delete').on('click',function () {
    deleteFromCompare(id)
    $('.to-compare[data-id="'+id+'"]').removeClass('active')

})