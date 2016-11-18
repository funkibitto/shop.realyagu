<div class="bs-example" data-example-id="basic-forms">
    {{ form('class': '') }}
        <div class="form-group">
            {{ form.render('userName', ['class' : 'form-control', 'placeholder' : '아이디를 입력하세요']) }}
            <input type="email" class="form-control" id="exampleInputEmail1" placeholder="Email">
        </div>
        <div class="form-group">
            <label for="exampleInputPassword1">Password</label>
            <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
        </div>
        <div class="checkbox">
            <label>
                <input type="checkbox"> Check me out
            </label>
        </div>
        <button type="submit" class="btn btn-default">Submit</button>
    </form>
</div>